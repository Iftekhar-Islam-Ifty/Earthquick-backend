# Phase 1 — Data Integrity and Safe Catalog Seeding

Status: Implemented

## Completed in this phase

- Vendor seeding now runs before catalog seeding.
- The core catalog seeder no longer truncates products, product images,
  categories or subcategories.
- Categories, subcategories and products use `firstOrCreate` keyed by their
  stable slugs, so repeated seed runs do not duplicate records.
- Vendor and Electronics category seed records are created only when missing;
  existing administrator-managed records are preserved.
- Nous Telos bag seed records are also created only when missing.
- Routine seeding no longer silently assigns unowned legacy products to Nous
  Telos.
- Products now have an explicit `is_active` lifecycle flag, separate from
  stock availability, and the central admin can publish/unpublish products.
- Public product, vendor-store and cart checks respect product lifecycle status.
- Added `catalog:integrity` for a read-only ownership/reference health report.
- Added `catalog:backfill-legacy-vendors` as a reviewed report-first backfill;
  it changes ownership only when explicitly run with `--apply`.

## Safety rule

`DatabaseSeeder` is now safe to run as a development/bootstrap seed against an
existing catalog, but it is not a replacement for a reviewed production data
backfill. Existing products with a null `vendor_id` require an explicit
inventory review before ownership is assigned.

## Verification

- Local catalog integrity report: 17 products, 0 unassigned, 0 inactive,
  0 missing SKU and 0 invalid subcategory references.
- The reviewed backfill report found 0 unassigned products and made no changes.
- Automated idempotence and lifecycle tests are included in the feature suite.

The next phase can build on this foundation for checkout/order correctness and
payment/delivery behavior.
