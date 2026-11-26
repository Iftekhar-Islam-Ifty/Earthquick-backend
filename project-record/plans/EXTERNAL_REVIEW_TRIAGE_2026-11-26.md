# External review triage — 2026-11-26

Initial triage record with follow-up progress below. Current source is the implementation authority. Review
base: `main` at `280c7a6`, with existing uncommitted Phase 11 hardening changes
preserved. The external review is a source of hypotheses, not proof of a live
defect. No live browser/cPanel revision was verified for this record.

## Decision boundaries

- Rthquick remains the platform and central admin; Nous Telos and Bright are
  stores. Heaven is a Bright product brand, conditional on Bright confirmation.
- Keep the existing Laravel/Blade app, shared checkout, order snapshots and
  order-level fulfillment. No seller portal, payout system, gateway integration,
  framework upgrade or broad rewrite is a launch task here.
- The initial review changed plans only. Implement and verify individual tasks
  incrementally, preserving the active Phase 11 security work.

## Evidence and ownership

| Finding | Current evidence and status | Impact | Priority | Primary record |
|---|---|---|---|---|
| Checkout shares stock between legacy size-labelled cart lines | **Fixed locally; verification pending.** At triage, size-labelled cart lines validated separately against shared product stock. Checkout now locks and aggregates by product or variant stock pool, validates combined demand, and decrements each pool once. Cart add/update use the same pool accounting. | Oversell or wrong remaining stock | **Launch blocker until verified** | This plan, checkout correctness |
| Repeat checkout POST | **Fixed locally; verification pending.** The order number is now a stable, server-derived key for the same session/cart/form submission. Its existing database unique constraint is reserved before stock and coupon changes; retries resolve to the first order. | Duplicate order and customer confusion | **Launch blocker until verified** | This plan, checkout correctness |
| Escaped category separator | **Fixed locally and visually checked.** Product category labels render a bullet between escaped category names. | Visible markup text | **Before handover** | This plan, product polish |
| Product details placeholder | **Confirmed template fallback.** `product.blade.php:276` renders it when description is absent. Whether launch listings actually lack descriptions requires data review. | Thin product information | **Client decision/data pending** | Phase 10 Phase 8 |
| Two Kotki Saree listings at different prices | **Verification pending.** Current source does not establish live catalog equality or approved price. | Possible buyer confusion | **Client decision/data pending** | Phase 10 Phase 8 |
| Marketplace hero photography and cross-store product cards | **Design proposal.** `home.blade.php:11-35` has abstract graphic; store cards and category links already provide both discovery paths (`home.blade.php:36-88`). Photography/card additions require approved assets and verified products. | Clarifies assortment, but can distract from store choice | **Post-launch improvement** | Phase 10 visual follow-up |
| Shop and Stores destinations | **Confirmed intentional current behavior.** `/shop` redirects to `/stores` (`routes/web.php:44-46`); categories have direct links (`home.blade.php:75-87`). Revisit labels/navigation after buyer observation; route unchanged now. | Potential duplicate navigation labels | **Post-launch improvement** | Phase 10 journey follow-up |
| Nous Telos first-slide CTA, carousel and reveal | **Verification pending.** Store uses a dedicated homepage (`VendorController.php:47-57`). Inspect actual first slide, keyboard control, reduced-motion and reveal visibility before altering design. | Discoverability/accessibility | **Before handover** | Phase 10 Phase 7 visual QA |
| Bright hero text and thin product section | **Old screenshot may be stale.** Current view includes `vendor.partials.bright-hero` and product grid/empty state (`bright-home.blade.php:9-10,56-68`); Phase 10 Phase 8 records demo hero. Live deployed revision is unknown. | Misleading or sparse storefront if demo presented as final | **Client decision/data pending** | Phase 10 Phase 8 |
| Product selector/action spacing | **Verification pending.** Product markup places size before actions (`product.blade.php:149-207`); responsive overrides exist. Need rendered desktop/mobile review to identify actual gap. | Friction to purchase | **Before handover** | Phase 10 Phase 7 visual QA |
| bKash | **Already deferred.** Server rejects bKash before creating an order (`CheckoutController.php:124-131`); COD remains supported. | Prevents false payment claim | **Client decision/data pending** | Phase 7 |
| Production security, SMTP, backup, document root | **Verification pending.** Phase 11 and Phase 6 contain handoff steps; local source/config is not proof of cPanel state. | Security and operational release risk | **Launch blocker** | Phase 11, with Phase 6 cross-reference |
| Large AdminController and checkout extraction | **Design recommendation.** AdminController is about 1,175 physical lines and manages several domains. Extract only alongside future feature work after behavior is protected. | Maintainability, lower immediate buyer impact | **Post-launch improvement** | This plan |

## Ordered implementation and verification work

1. **Checkout inventory correctness — launch blocker.** Aggregate demand by
   actual stock pool (product ID for legacy size labels, variant ID for true
   variants) inside the transaction. Lock each pool, validate its combined
   quantity, then decrement exactly once. Preserve each customer's size and
   variant snapshot. Regression scenarios: two size-labelled lines whose sum
   exceeds stock must fail without order/stock changes; an allowed sum must
   create one order with correct stock; two independent variants remain separate.
   Trace cancellation and return restock against the same pool. Dependency:
   existing stock semantics and isolated test database. Primary tracking here.
2. **Checkout repeat-submit protection — launch blocker.** Add a server-issued
   one-time checkout token or equivalent idempotency mechanism with a unique
   persisted constraint; retry must resolve to the original order and cannot
   decrement stock or redeem coupon twice. UI button disabling is supplemental.
   Dependency: task 1 and reviewed DB migration/backup if schema changes.
3. **Production handoff — launch blocker.** Verify deployed commit, HTTPS and
   Laravel public document root, APP_DEBUG off, PHP/extensions, secure cookies,
   admin email verification/MFA, SMTP and scheduler, database/uploads backup and
   restore, then rollback path. Mark each item verified only with cPanel/staging
   evidence. Dependencies: hosting access, production backup and Phase 11 code
   deployment. Primary record: Phase 11.
4. **Catalog and buyer journey QA — before handover.** Review desktop, tablet,
   390px and 360px: home → store → catalog → product → cart → checkout;
   search/filter/sort, menu touch/keyboard, carousel/reduced motion, hero crop,
   product controls and no horizontal overflow. Fix only observed issues and
   keep dynamic labels escaped. Replace the literal `&bull;` separator with
   safe markup or a real bullet. Dependency: running current build and images.
5. **Staging order acceptance — before handover.** In an isolated database,
   cover guest COD, mixed-store seller snapshots, variant stock, coupons,
   inside/outside Chattogram fees and free-shipping threshold, receipt/invoice,
   status, cancellation, return/restock, applicable refund and mail retry.
   Expected totals/statuses must follow the current approved business rules.
   No live order is needed. Dependency: tasks 1–2 and staging data.
6. **Verified catalog and handover — client data pending.** Collect Bright
   product name, model/SKU, category, approved images/specs, price, stock,
   brand attribution and Bright-confirmed warranty/returns. Confirm any
   duplicate Nous Telos listings and final descriptions with their owner.
   Use the existing Bright CSV intake template and central admin. Give one
   admin training session plus a short upload/order-handling guide. Do not
   hold Nous Telos or central checkout verification for Bright data.
7. **Optional design and code cleanup — post-launch.** Revisit marketplace
   photography/product cards and Shop label with owner-approved assets and
   observed buyer behavior. Extract an AdminController slice when its next
   feature is touched. Consider a focused PlaceOrder action only after stock
   and retry behavior are correct; preserve routes, authorization, transaction
   and after-commit communications boundaries.

## Staging expectations and outstanding inputs

- Free shipping, COD fees, coupon calculation, seller/order snapshots and
  after-sales outcomes must match current configured business rules; no new
  warranty or return duration is assumed here.
- Owner: approve final hero/product assets, clarify any duplicate Nous Telos
  listing, choose whether Shop should become a catalog entry after observing QA.
- Bright: verified commercial catalog, approved images, Heaven attribution and
  warranty terms. Current demo artwork is a replacement target, not product
  evidence.
- Hosting: identify actual Laravel document root and deployed revision. Recent
  Softaculous WordPress screen referred to the same `public_html` path; do not
  run that WordPress upgrade as a Laravel deployment step.

The first implementation task was **checkout inventory correctness**. It can
change accepted order quantities and stock balances, so its local fix precedes
visual polish and architectural extraction; verification remains open.

## Follow-up implementation — 2026-11-26

- Checkout now locks distinct product and variant inventory rows in ID order,
  sums quantity across every cart line sharing a stock pool, rejects excess
  demand before creating an order, and decrements each pool once. The order
  still records each size or variant as a separate item snapshot.
- Cart add and quantity update now count other lines drawing on the same stock
  pool. Cancellation and return restock already use the saved variant ID, or
  the product ID for legacy size labels; their existing path remains unchanged.
- Added focused regression cases for rejected and accepted legacy size demand,
  cart limits, and independent variant pools. PHP syntax and diff checks pass.
  Automated tests were not run under the project instruction. No migration,
  seeder, live order, or Phase 11 file was changed by this follow-up.
- Repeat-submit protection is implemented locally after the stock fix. The
  checkout order number now uses a longer 96-bit HMAC suffix derived from the
  server key, session ID, cart, checkout fields and a nonce. The existing
  unique `orders.order_number` index is the persisted constraint, so no schema
  migration is needed. A committed duplicate returns the first receipt without
  sending a second placement notification, decrementing stock or redeeming a
  coupon again. A fresh purchase rotates the nonce. Old order numbers are
  untouched. Focused retry/new-purchase regression cases were added but not
  run under the project instruction. Production/security handoff remains open.

## UI-first follow-up — 2026-11-26

- Current local Chrome review covered the marketplace landing page, both store
  homepages, Bright catalog, and a Nous Telos product page. Product controls
  were inspected at 390px, 360px and 768px; the Bright catalog at 390px and
  360px. Those reviewed layouts had no horizontal overflow.
- Fixed the rendered `&bull;` text in the product category label. Increased
  small-screen category/stock/option text and option/purchase control heights
  without changing the desktop product layout. The product label now renders
  `WOMEN • SAREE` in Chrome.
- The Nous Telos hero arrows now have stronger contrast. Keyboard arrow input
  was observed changing slides. The carousel now pauses automatic sliding
  while focused or the page is hidden, and does not auto-slide for reduced
  motion. Reduced-motion behavior is source-reviewed, not browser-emulated yet.
- The first Nous Telos hero currently contains lettering baked into its image.
  The owner previously requested an image-only hero, so a new HTML CTA or
  overlay headline was not added. Removing that lettering needs a replacement
  approved image. Marketplace product photography and Bright real-product
  imagery also remain dependent on approved assets and verified catalog data.
- Task 4 remains open for variant-product controls, reduced-motion browser
  emulation and the complete desktop/tablet buyer journey. No order, migration,
  seeder or automated test was run in this follow-up.
- Follow-on local Chrome review found the mobile cart drawer header hidden behind
  the sticky navbar and the separate `/cart` page's two-column grid overlapping
  at 390px. The drawer stacking order and dedicated cart-page layout were fixed;
  cart and checkout were visually checked at 390px and 360px, and the desktop
  cart kept its two-column layout. A temporary session cart item was removed
  after review; no order was submitted. Checkout's breadcrumb now links to
  neutral Stores instead of the unrelated Women category.
- Bright catalog category selection, search, ascending price sort and the warm
  filter panel were checked in local Chrome. Tablet Shop toggle was checked at
  820px; Escape now hides the popup even while the toggle retains pointer
  hover. Full variant-product and reduced-motion browser checks remain open.
