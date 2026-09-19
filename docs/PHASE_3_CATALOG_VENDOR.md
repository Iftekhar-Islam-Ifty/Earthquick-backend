# Phase 3 — catalog and vendor integrity

Completed 2026-09-18. This phase keeps Earthquick under one central admin. It
does not add seller dashboards, vendor management portals, commission, payout,
or split-fulfilment features.

## Changes

- Public catalog queries now include products without a `vendor_id` (legacy
  inventory) and products whose assigned vendor is active. Products attached to
  an inactive vendor are hidden from home, category/subcategory, search,
  suggestions, product-detail and related-product queries.
- Cart add rejects products belonging to an inactive vendor with a 422 response.
  Stock, quantity and checkout-time revalidation remain Phase 4 work.
- Central admin product validation rejects a subcategory that belongs to a
  different category.
- Removed the non-functional vendor commission and featured-vendor controls from
  the central admin controller and create/edit forms. Product-level featured
  status remains available because it is a valid catalog presentation field.
- Development seeding now loads the original catalog before vendor setup,
  preserves the Bright Electronics category, and attaches seed-created legacy
  products to Nous Telos. The seeder is still destructive and must never be run
  against real inventory merely to backfill vendor IDs.

## Verification

`composer test` passed: **73 PHP tests, 385 assertions, and 10 frontend
interaction tests**.

`composer test:regressions` now has **8 expected failures**, all assigned to
Phase 4: stock/quantity validation, checkout price and inventory revalidation,
vendor recheck at checkout, delivery consistency, and order privacy.

Browser visual verification remains pending because no browser was connected.
