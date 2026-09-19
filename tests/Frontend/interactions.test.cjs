const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');

function target(extra = {}) {
  const listeners = new Map();
  return Object.assign({
    addEventListener(type, fn) {
      if (!listeners.has(type)) listeners.set(type, new Set());
      listeners.get(type).add(fn);
    },
    removeEventListener(type, fn) { listeners.get(type)?.delete(fn); },
    dispatch(type, event = {}) {
      for (const fn of [...(listeners.get(type) || [])]) {
        fn({ preventDefault() {}, stopPropagation() {}, ...event });
      }
    },
    classList: { add() {}, remove() {}, contains() { return false; } },
    setAttribute() {},
  }, extra);
}

function harness(base = 'http://localhost/earthquick/public') {
  const requests = [];
  const timers = new Map();
  let timerId = 0;
  const modalAdd = target();
  const body = { innerHTML: '', querySelector: () => modalAdd };
  const modal = target();
  const card = { dataset: { id: '7' }, id: 'card-7', querySelector: () => null };
  const button = target({ dataset: { action: 'add-to-cart' }, closest: () => card });
  const searchInput = target({ value: '' });
  const searchModal = target();
  const searchOpen = target();
  const searchResults = { style: {}, innerHTML: '' };
  const selectors = {
    'meta[name="app-url"]': { getAttribute: () => base },
    '#eq-quickview-modal': modal, '#eq-quickview-body': body,
    '#eq-search-modal': searchModal, '#eq-search-input': searchInput,
    '#eq-search-live-dropdown': searchResults,
  };
  const document = target({
    readyState: 'loading', body: { style: {} }, activeElement: null,
    createElement() {
      return { textContent: '', get innerHTML() { return this.textContent; } };
    },
    querySelector: selector => selectors[selector] || null,
    querySelectorAll(selector) {
      if (selector === '[data-action="add-to-cart"], [data-action="quick-view"]') return [button];
      if (selector === '[data-action="open-search"]') return [searchOpen];
      return [];
    },
  });
  const context = {
    document, window: target({ scrollY: 0, innerWidth: 800 }), console, AbortController,
    setTimeout(fn) { timers.set(++timerId, fn); return timerId; },
    clearTimeout(id) { timers.delete(id); },
    fetch: async (url, options) => {
      requests.push({ url, options });
      return { ok: true, json: async () => ({ success: true, items: [], count: 1, subtotal: 100, suggestions: [] }) };
    },
  };
  vm.createContext(context);
  vm.runInContext(fs.readFileSync(path.join(__dirname, '../../public/js/script.js'), 'utf8'), context);
  vm.runInContext('Toast.show = () => {}', context);
  const cart = context.window.EarthquickCart;
  cart.buildDrawerDOM = () => {};
  cart.updateBadges = cart.renderDrawer = cart.openDrawer = () => {};
  return {
    context, document, selectors, card, button, modalAdd, body, cart, requests, searchInput, searchResults,
    run: source => vm.runInContext(source, context),
    flushTimers() { const pending = [...timers.values()]; timers.clear(); pending.forEach(fn => fn()); },
  };
}

test('reinitialization sends one add request per explicit add click', async () => {
  const h = harness();
  h.cart.init(); h.cart.init();
  h.button.dispatch('click');
  await new Promise(setImmediate);
  assert.equal(h.requests.filter(r => r.url.endsWith('/cart/add')).length, 1);
});

test('inspect opens preview without adding; modal add posts the correct product once', async () => {
  const h = harness();
  h.button.dataset.action = 'quick-view';
  h.run('initQuickViewModal(); initQuickViewModal();');
  h.cart.init(); h.cart.init();
  h.button.dispatch('click');
  assert.match(h.body.innerHTML, /quickview-add-bag/);
  assert.equal(h.requests.filter(r => r.url.endsWith('/cart/add')).length, 0);
  assert.doesNotThrow(() => h.modalAdd.dispatch('click'));
  await new Promise(setImmediate);
  const adds = h.requests.filter(r => r.url.endsWith('/cart/add'));
  assert.equal(adds.length, 1);
  assert.deepEqual(JSON.parse(adds[0].options.body), { product_id: 7, quantity: 1, size: 'Standard' });
});

for (const base of ['http://localhost', 'http://localhost/earthquick/public/']) {
  test(`cart and coupon endpoints respect ${base}`, async () => {
    const h = harness(base);
    await h.cart.fetchCart(); await h.cart.addItem(7);
    await h.cart.updateQty('7_standard', 1); await h.cart.removeItem('7_standard');
    await h.cart.clearCart(); await h.cart.applyCoupon('TEST'); await h.cart.removeCoupon();
    assert.deepEqual(h.requests.map(r => r.url),
      ['/cart', '/cart/add', '/cart/update', '/cart/remove', '/cart/clear', '/cart/coupon/apply', '/cart/coupon/remove']
        .map(route => base.replace(/\/$/, '') + route));
  });
}

test('search reinitialization makes one request and keeps subfolder result links', async () => {
  const h = harness();
  h.run('initSearchModal(); initSearchModal();');
  h.searchInput.dispatch('input', { target: { value: 'silk saree' } });
  h.flushTimers();
  await new Promise(setImmediate);
  assert.equal(h.requests.length, 1);
  assert.equal(h.requests[0].url, 'http://localhost/earthquick/public/api/search/suggestions?q=silk%20saree');
  assert.match(h.searchResults.innerHTML, /http:\/\/localhost\/earthquick\/public\/search\?q=silk%20saree/);
});

test('clearing search aborts a previous in-flight request', () => {
  const h = harness(); h.run('initSearchModal()');
  h.searchInput.dispatch('input', { target: { value: 'silk' } }); h.flushTimers();
  h.searchInput.dispatch('input', { target: { value: '' } }); h.flushTimers();
  assert.equal(h.requests[0].options.signal.aborted, true);
});

test('reinitializing search cancels work belonging to the old component', () => {
  const h = harness(); h.run('initSearchModal()');
  h.searchInput.dispatch('input', { target: { value: 'silk' } }); h.flushTimers();
  h.searchInput.dispatch('input', { target: { value: 'cotton' } });
  h.run('initSearchModal()'); h.flushTimers();
  assert.equal(h.requests.length, 1);
  assert.equal(h.requests[0].options.signal.aborted, true);
});

test('navbar reinitialization toggles the mobile menu once', () => {
  const h = harness();
  const classList = () => {
    const values = new Set();
    return {
      add: name => values.add(name), remove: name => values.delete(name),
      contains: name => values.has(name),
      toggle(name, force) {
        const enabled = force ?? !values.has(name);
        if (enabled) values.add(name); else values.delete(name);
        return enabled;
      },
    };
  };
  const navbar = target({ classList: classList(), querySelector: () => null, querySelectorAll: () => [] });
  const toggle = target({ classList: classList() });
  const links = target({ classList: classList(), querySelectorAll: () => [] });
  h.selectors['#eq-main-navbar'] = navbar;
  h.selectors['#eq-nav-toggle'] = toggle;
  h.selectors['#eq-nav-links'] = links;
  h.document.getElementById = () => null;
  h.document.body.classList = classList();
  h.document.documentElement = { classList: classList() };
  h.run('initNavbar(); initNavbar();');
  toggle.dispatch('click');
  assert.equal(links.classList.contains('is-open'), true);
  toggle.dispatch('click');
  assert.equal(links.classList.contains('is-open'), false);
});

test('quick-view treats product titles as text rather than HTML', () => {
  const h = harness();
  h.card.querySelector = selector => selector.includes('__name') ? { textContent: '<img onerror="bad()">' } : null;
  h.run('initQuickViewModal()'); h.context.window.openQuickView(h.card);
  assert.match(h.body.innerHTML, /&lt;img onerror=&quot;bad\(\)&quot;&gt;/);
  assert.doesNotMatch(h.body.innerHTML, /<img onerror="bad\(\)">/);
});

test('checkout and relative image URLs use the same application root', () => {
  const h = harness();
  assert.equal(h.run('appUrl("/checkout")'), 'http://localhost/earthquick/public/checkout');
  assert.equal(h.run('appUrl("images/products/test.jpg")'), 'http://localhost/earthquick/public/images/products/test.jpg');
});
