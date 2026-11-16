# Bright Phase 1 - Store, brand and warranty foundation

**Status: In progress.** Started 2026-11-16 at the owner's request.

## Implementation checkpoint - 2026-11-16

The first foundation slice is implemented locally and its migration is applied.
The `product_brands` table now has an owner store and warranty-provider store;
the local database contains Heaven owned by Bright with Bright as warranty
provider. Products can hold an optional brand, and central-admin product forms
filter brands by selected store and enforce ownership on save. Product detail,
cart/checkout summaries and order records distinguish brand, seller and warranty
provider. New order lines snapshot those names. Existing order lines retain null
snapshots and continue to render without changing historical data.

Before applying the migration, a full private local database backup was written
to ignored `storage/qa/pre-bright-phase1-20261116/` storage. The migration is
recorded as applied in the local database.

Remaining in Phase 1: review the complete UI flow in a browser, refine copy/layout,
and enter Heaven product details only when Bright supplies actual product and
warranty terms. Automated tests were not run in this checkpoint.

## Implementation checkpoint - 2026-11-16

The first foundation slice is implemented locally and its migration is applied.
The `product_brands` table now has an owner store and warranty-provider store;
the local database contains Heaven owned by Bright with Bright as warranty
provider. Products can hold an optional brand, and central-admin product forms
filter brands by selected store and enforce ownership on save. Product detail,
cart/checkout summaries and order records distinguish brand, seller and warranty
provider. New order lines snapshot those names. Existing order lines retain null
snapshots and continue to render without changing historical data.

Before applying the migration, a full private local database backup was written
to ignored `storage/qa/pre-bright-phase1-20261116/` storage. The migration is
recorded as applied in the local database.

Remaining in Phase 1: review the complete UI flow in a browser, refine copy/layout,
and enter Heaven product details only when Bright supplies actual product and
warranty terms. Automated tests were not run in this checkpoint.

## Confirmed structure

- Rthquick is the marketplace.
- Nous Telos and Bright are the two stores.
- Bright is the electrical-products store and seller.
- Heaven is a product brand owned by Bright; it is not a separate store.
- Bright provides the warranty for Heaven products. Warranty duration and terms
  must come from Bright; this phase does not invent them.

## Phase 1 scope

1. Add first-class product-brand data linked to its owning store and default
   warranty provider.
2. Add an optional brand association to products without changing legacy items.
3. Seed Heaven under Bright as the brand and warranty provider.
4. Let central-admin product entry and editing select a store and one of that
   store's brands, with server-side ownership validation.
5. Display product brand, selling store and warranty provider separately.
6. Snapshot store, brand and warranty-provider names on order items so later
   profile edits do not rewrite old receipts.

No seller portal, commissions, payouts, new product claims or independent Heaven
store are part of this phase. Full brand management, brand filters, additional
brand onboarding, product data entry and wider storefront refinement remain later
slices.
