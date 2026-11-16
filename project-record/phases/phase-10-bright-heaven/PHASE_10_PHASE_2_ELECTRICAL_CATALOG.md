# Bright Phase 2 - Electrical catalog foundation

**Status: In progress.** Started 2026-11-16 at the owner's request.

## Verified source scope

The owner supplied a screenshot of Bright Electrical Company's Facebook page.
It identifies Bright as a wholesale and retail electrical supplier in
Chattogram and lists switches, sockets, LED lights, circuit breakers, fans and
other electrical accessories. Its cover lists the office at Police Plaza,
Nandankanan, Chattogram. These are the categories and profile facts in scope for
this first Phase 2 slice.

## Phase 2 sequence

1. Replace the seeded prototype copy and placeholder contact details on Bright's
   vendor record with the information shown in the supplied page screenshot.
2. Add shared Electronics subcategories for Switches, Sockets, LED Lights,
   Circuit Breakers, Fans and Electrical Accessories.
3. Guide and validate Bright product entry under Electronics and electronics
   product type, while keeping its Heaven brand attached from Phase 1.
4. Identify Nous Telos and Bright as stores in customer/admin navigation,
   directories and seller filters; reserve Brand labels for product brands such
   as Heaven. Preserve Nous Telos's existing fashion-oriented store copy.
5. Add Bright product listings only from facts Bright has supplied; do not invent
   SKUs, prices, specs, certifications, manufacturer claims or warranty terms.

No product listings exist for Bright in the local database at Phase 2 start.
The first slice therefore prepares the catalog and seller record; actual product
entry awaits product data from Bright.

## Implementation checkpoint - 2026-11-16

The first catalog slice is implemented locally. Bright's prototype profile copy
and placeholder email/phone were conditionally replaced with the supplied page's
store name, supplier description, wholesale/retail tagline and office address.
Six shared subcategories were created under Electronics. Central-admin product
entry now guides Bright products to Electronics and enforces that category and
electronics product type on save. Bright's storefront uses product/catalog
language while Nous Telos keeps its existing fashion wording.

The migration is applied locally after a private pre-migration database backup.
Admin product data entry and order/cart warranty-provider labels from Phase 1
were also tightened during this work. Bright currently has zero products, so no
SKU, price, stock or specifications were added. PHP syntax checks and Blade
compilation passed; automated tests and browser visual review were not run.

The store/brand distinction is now reflected in navigation, store directory,
store pages, admin vendor management, product-store filters, about content and
invoice identity. Product brand filters/labels remain available for actual
product brands; vendor-backed filter fields are labelled Store in the UI.

The Bright storefront now shows its Electronics subcategories (switches,
sockets, LED lights, circuit breakers, fans and electrical accessories) as the
browse links instead of repeating the parent Electronics category. Store filters
open as a compact panel aligned beside sorting on both Bright and Nous Telos
pages, with a full-width layout on small screens.

Bright category selection validates against active Electronics subcategories and
filters by the selected subcategory ID. A valid but empty subcategory now shows
an empty result rather than dropping the selection and showing every product.
Focused storefront filter/sort regression checks pass.

## Demo listings - 2026-11-16

At the owner's request, six clearly marked, replaceable `[DEMO]` listings were
added locally for switches, sockets, LED lights, circuit breakers, fans and
electrical accessories. Each is assigned to Bright, the Heaven brand and its
matching Electronics subcategory. The demo prices and stock are only for trying
the storefront, product page, cart and checkout; they are not Bright quotations.
Simple custom SVG illustrations identify each product type. No product
specifications or warranty terms were invented, and the demo records state that
Bright's return policy has not been provided. Replace these with Bright-verified
catalog data before launch. `BrightDemoProductsSeeder` is safe to rerun and
updates only these six demo slugs. The local database was backed up at
`storage/qa/pre-bright-demo-products-20261116/earthquick_db-pre-bright-demo-products.sql`
before seeding. No schema migration was needed. These local demo listings have
not been pushed or deployed.

## Reviewed local data change

Preflight on 2026-11-16 found zero Bright products and zero existing Electronics
subcategories. The data migration will create the six listed shared
subcategories, update Electronics' prototype description, and correct Bright's
vendor name/tagline/description/address only when fields still equal their
known seeded prototype values. It clears only the known placeholder Bright
email/phone values. It will not change product, order or customer rows. Before
applying it locally, create a private database backup.
