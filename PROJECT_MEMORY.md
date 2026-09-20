# Earthquick: project memory and architecture review

## Current scope update — Phase 2

On 2026-09-20, the general marketplace catalog work added product types,
structured specifications, warranty/support data and database-backed product
variants. Variants carry their own SKU, attributes, optional price, active
status and stock. Cart and checkout remain backward-compatible with products
without variants; variant products are revalidated and decremented atomically,
and order items retain variant snapshots. See
`docs/PHASE_2_GENERAL_CATALOG.md`. The full suite passes 94 PHP tests / 517
assertions and 10 frontend tests.

The owner has authorized and completed Phase 1 (isolated test setup), Phase 2
(shared frontend URL/event/quick-view/DOM corrections), Phase 3 (catalog and
vendor integrity), Phase 4 (checkout and order correctness), and Phase 5
(platform identity and flagship editorial scope). See
`docs/PHASE_5_PLATFORM_IDENTITY.md`: `composer test` passes 83 PHP tests / 411
assertions and 10 JS tests. There is no separate vendor dashboard/portal or
commission/payout system. Phase 5 presents Earthquick as the marketplace,
preserves Nous Telos as its flagship heritage store, gives Bright an honest
electronics-store-in-preparation presence, and scopes home fashion editorial
queries to Nous Telos. Browser visual verification remains unavailable; HTTP and
automated checks do not replace it.

Reviewed: 2026-09-18. This is repository-local persistent context for future work.
It is not a claim of account-wide or permanent assistant memory.

## Owner's confirmed intent

Earthquick is a multivendor website. Nous Telos is the main initial vendor, a real
Bengali heritage/handloom shop comparable in category to Aarong, which commissioned
the project. Bright is another vendor, an electronics shop, initially an example
to develop fully later. More vendors will follow. The original implementation
over-associated the platform with Nous Telos. Preserve the existing work and make
targeted changes instead of rewriting the website. Nous Telos should remain the
most detailed and prominent store without defining every vendor's identity.

## Confirmed Phase 0 operating rules

- Earthquick has one central admin for all vendors; no vendor dashboards or seller logins are required.
- A customer may place products from multiple vendors in one combined order.
- Delivery, customer-facing status and tracking remain order-wise.
- Earthquick central admin owns cancellation, return and refund decisions.
- The initial payment methods are COD and bKash.
- Suspending a vendor stops new purchases while existing confirmed orders remain for admin review and resolution.
- Commission, payout and vendor settlement systems are not in scope.

## Review scope and verification limits

Reviewed application routes, core controllers and models, schema migrations,
seeders, storefront/admin template references, frontend asset structure, test
coverage definitions, build configuration, and existing documentation. This is a
source-level architecture review, not a browser visual audit or exhaustive
security audit. No production/local business records were inspected or changed.
Seed definitions below are not verified live database counts.

`php artisan route:list --except-vendor --no-ansi` successfully listed 51 routes.
No migrations or seeders were run. Feature tests were read, not executed: the
PHPUnit database isolation settings are commented out and several tests depend
on existing seeded data. Their upload/delete operations can affect public files
even where database transactions roll back. No passing-test claim is made.

At review start, vendor-related implementation files were already modified or
untracked. Those changes were treated as existing user work and left intact.

## Architecture and file map

- Backend: PHP ^8.2, Laravel ^12.0 (`composer.json`), Eloquent, server sessions.
  Seed SQL is MySQL-specific. Deployment is documented for XAMPP/cPanel.
- Frontend: server-rendered Blade and custom vanilla JavaScript. The storefront
  loads `public/css/style.css`, `public/css/responsive.css`, and
  `public/js/script.js` directly. Vite 7/Tailwind 4 tooling also exists, but is not
  the main storefront styling path.
- `routes/web.php`: catalog, store directory, product, cart, checkout, search,
  customer authentication/account, and central admin routes.
- `HomeController` and `home.blade.php`: editorial landing page, featured/new
  products, saree and bag collections. Home has its own HTML document, separate
  from `layouts/app.blade.php`; shared metadata changes must consider both.
- `CategoryController`, `SearchController`, `ProductController`: category and
  subcategory browsing, fabric/price/stock filters, sorting, live suggestions,
  product gallery, and related products.
- `VendorController`, `resources/views/vendor/`: `/stores` directory and
  `/stores/{slug}` with per-vendor catalog/category/price filtering and pagination.
- `CartController`: session cart, product/size keys, AJAX quantity changes,
  coupon application/removal, vendor identity in cart entries.
- `CheckoutController`: guest or authenticated checkout; transactional order and
  item creation; coupon revalidation; vendor ID copied to order items. Shipping
  is BDT 80 inside Chattogram and 150 outside. COD/bKash/card values are accepted,
  but no payment gateway charging/verification flow was found in application code.
- `AuthController`, `AccountController`: email/phone login, registration, profile
  editing, order history and tracking. Access uses customer identity/contact data.
- `AdminController`: dashboard/analytics, order status/courier/tracking, printable
  invoice, CSV export, product/image management, stock toggle, coupons.
- `AdminVendorController`: central admin vendor creation/editing, image uploads,
  listing/search, active status. `AdminMiddleware` uses `users.is_admin`.
- `tests/Feature/`: catalog/search, auth/account, cart/checkout, admin, vendor tests.
- `CPANEL_DEPLOYMENT_GUIDE.md` and `PROJECT_ARCHITECTURE_STUDY_GUIDE.html`:
  useful historical context, but their Laravel 11 labels conflict with the current
  Laravel 12 dependency declaration. README was Laravel boilerplate before review.

## Data model and current multivendor foundation

`Category -> Subcategory`; products belong to a category, optionally a subcategory,
and optionally a vendor. Products have gallery images. `Order -> OrderItem`, with
an optional customer account; each item references its product and optional vendor.
Coupons currently apply to the whole cart/order.

The vendor migrations add `vendors` and nullable `vendor_id` foreign keys to
`products` and `order_items`. Vendor profiles include name, unique slug/code,
logo/banner, tagline/description, contacts, active status and display order.
There is no vendor-account relationship, seller role/dashboard, settlement ledger,
or per-vendor shipment/order status implementation.

Product admin supports vendor selection and filtering. New SKU generation uses
the vendor code. Product details and selected catalog/admin order views display
vendor identity. Public store routes exclude inactive vendors. This is an
admin-managed multivendor catalog foundation, not a complete seller marketplace.

`VendorSeeder` defines Nous Telos (`nous-telos`, `NT`) and Bright (`bright`, `BRT`),
plus an Electronics category. Bright's current sample copy emphasizes lighting;
the owner's broader electronics intent takes precedence. Seed contact details
and marketing claims should be treated as placeholders until confirmed.

`EarthquickSeeder` defines 13 Nous Telos products across six categories: Men,
Women, Kids, Ornaments, Bags, Home Decor. Subcategories include Saree, Three Piece,
Two Piece, Kantha, Bedsheet, Cushion Cover. Bright has no products in these seeds.

## Findings to carry into implementation

1. **Platform/vendor branding is mixed.** Layout/home SEO, footer (including
   "A Nous Telos brand"), mobile drawer, checkout, and search/cart fallbacks assume
   Nous Telos. Store directory language also assumes artisans/ateliers. About
   presents hardcoded Nous Telos Living and Earthquick Studio instead of reflecting
   the intended initial Nous Telos/Bright pair. Separate platform-wide text from
   Nous Telos-specific editorial content.
2. **Home queries are not vendor-scoped.** Featured/new lists query all products;
   future electronics could appear in fashion-oriented sections. Explicitly scope
   Nous Telos sections and use general presentation for marketplace sections.
3. **Vendor suspension is incomplete.** Store pages require active vendors, but
   general home/category/search/product/cart/checkout paths do not consistently
   enforce vendor activity. There is no global Product scope enforcing it.
4. **Vendor admin fields do not match schema.** Controller accepts/writes
   `commission_rate` and `is_featured`, but neither exists in the vendor migration
   or Vendor fillable list. These are not working persisted vendor capabilities;
   decide whether to implement or remove their UI expectations.
5. **Seed order removes Electronics.** DatabaseSeeder runs VendorSeeder, then
   EarthquickSeeder, which truncates categories/subcategories/products and recreates
   only the six original categories. Electronics is therefore removed by the
   normal full seed sequence. This destructive seeder is not a data migration.
6. **Existing data needs an explicit backfill strategy.** Vendor migrations only
   add nullable IDs; they do not assign existing products or historical items.
   Do not run the destructive catalog seeder to attach real inventory to Nous Telos.
7. **Product presentation is fashion-oriented.** `fabric`, garment sizing/care,
   craftsmanship descriptions and handloom fallbacks need conditional rendering
   for electronics. No structured electronics specifications/warranty or general
   variant inventory model is present. Categories are shared, not vendor-owned.
8. **Order fulfillment remains centralized.** A mixed-vendor cart can record
   item ownership, but delivery fee/status/courier/tracking are order-wide. Seller
   payouts, commission calculation, split shipments and vendor-specific coupons
   are absent. These are future capabilities, not automatically initial scope.
9. **Checkout needs further correctness work before expansion.** Prices are read
   from session snapshots; stock/quantity/vendor availability is not revalidated
   and inventory is not decremented in checkout. Cart advertises free shipping
   at BDT 3,000, while checkout still applies its fixed geographic fee.
10. **Some UI is demonstration-only.** About/contact form shows a success message
    and resets without sending the inquiry to a backend. Do not equate a visible
    form or payment choice with a completed integration.

## Recommended incremental direction (not implemented by this review)

1. Establish Earthquick's platform identity in common navigation, SEO, footer,
   checkout and About. Keep Nous Telos prominent as the flagship vendor.
2. Reuse the store directory and vendor profiles; enrich Nous Telos's own store
   with its existing heritage content. Give Bright an appropriate electronics
   introduction and honest coming-soon catalog presentation.
3. Correct seed ordering and vendor field mismatches; migrate/backfill real data
   non-destructively; apply consistent vendor visibility/purchasability rules.
4. Scope vendor editorial queries and make product attributes/copy conditional.
   Preserve shared categories and existing routes wherever practical.
5. Verify core flows in a disposable seeded test database, including mixed-vendor
   orders, suspended vendors, historical ownership and electronics rendering.
6. Add independent seller accounts, commissions/payouts and split fulfillment only
   when the owner needs those operating capabilities. A central-admin initial
   phase is a recommendation, not a confirmed long-term business requirement.

## বাংলা সারাংশ

Earthquick মূল মার্কেটপ্লেস; Nous Telos তার প্রধান হেরিটেজ/হ্যান্ডলুম ভেন্ডর,
আর Bright ইলেকট্রনিকস ভেন্ডরের প্রাথমিক উদাহরণ। বর্তমান Laravel/Blade প্রজেক্টে
ক্যাটালগ, সার্চ, কার্ট, চেকআউট, কাস্টমার অ্যাকাউন্ট ও অ্যাডমিন প্যানেল আছে।
ভেন্ডর প্রোফাইল, স্টোর পেজ এবং পণ্য/অর্ডার আইটেমে ভেন্ডর সংযোগও ইতিমধ্যে আছে।
তবে সাধারণ ব্র্যান্ডিং এখনো Nous Telos-কেন্দ্রিক; ভেন্ডরভিত্তিক স্বাধীন পরিচালনা,
কমিশন নিষ্পত্তি ও আলাদা ডেলিভারি ব্যবস্থা নেই। পুরো সাইট পুনর্লিখন ছাড়াই বর্তমান
ভিত্তির ওপর ধাপে ধাপে প্রয়োজনীয় পরিবর্তন করা সম্ভব।
