# Phase 2 — shared frontend fixes

Completed 2026-09-18. Preserves the existing theme and central-admin structure.
No database migration, inventory/order business-rule change, vendor dashboard or
commission feature was introduced. Phase 3 has not started.

## Changes

- Both home and the shared layout expose Laravel's application root through an
  `app-url` meta tag. Cart/coupon endpoints, search requests/result links, checkout
  redirects and relative cart images use that root, including subfolder installs.
- Shared navbar/search/cart/quick-view listeners replace their previous binding
  instead of accumulating on reinitialization. Search aborts pending work when
  cleared or reinitialized and ignores responses from aborted requests.
- Category, search, store and related-product inspection buttons explicitly open
  quick view. Opening preview no longer navigates or adds a product. Add to Bag
  uses the real cart endpoint and product ID; the obsolete `cartCount` path is gone.
- Product-title links still navigate. Home overlays inside product links now say
  “View product”; styling classes alone never trigger cart additions.
- Quick-view text/attribute interpolation escapes product metadata. Existing
  handloom marketing copy and product-attribute redesign belong to later phases.
- Footer owns the single toast container. Both store templates supply a unique
  `main-content` landmark for the shared skip link.

## Verification

`composer test` passed: **68 PHP tests, 363 assertions, 10 JavaScript tests**.
The two former failing frontend regressions moved into the default green suite,
with additional tests for URL prefixes, preview-versus-add behavior, repeat
initialization, stale search work and safe text rendering.

- `composer test:frontend`: run the JS tests alone.
- `composer test:frontend-regressions`: retained as an alias of the passing JS suite.
- `composer test:regressions`: the 11 previously documented backend failures still
  belong to Phases 3/4; those controllers and rules were not changed here.
- `node --check public/js/script.js` and Composer manifest validation passed.
- Live local HTTP checks for home, Nous Telos, Bright and search returned 200,
  the correct `http://localhost/earthquick/public` app root, one toast container,
  and one main-content target per page.

JS tests use a mock DOM. Browser inventory still returned no connected browser;
desktop/mobile visuals, real keyboard focus and actual browser interaction have
not been verified. Do not interpret the HTTP checks as visual browser approval.

## Contracts for later changes

- Keep the application-root meta tag in every document that loads `script.js`.
- Card title links navigate. Use `data-action="quick-view"` for inspection or
  `data-action="add-to-cart"` for explicit additions, with a real card product ID.
- Do not attach navigation `onclick` handlers to inspection/add controls or use
  `.eq-product-card__quick-add` as an action selector; it is a presentation class.
- Preserve the shared modal IDs, one toast container, and one main-content target.
- Keep listener scopes distinct when several components listen on `document`.
- Cart stock/price/delivery validation and vendor suspension remain future backend
  fixes. Completing this phase does not make those outstanding issues resolved.
