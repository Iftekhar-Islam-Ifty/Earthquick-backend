# Phase 2 — General Marketplace Catalog Foundation

Status: Complete

## What changed

- Products now have a `product_type` field with supported values for apparel,
  accessories, electronics, home and general catalog items.
- Products now support JSON `specifications` for category-specific label/value
  data without forcing electronics to use fashion-only fields.
- Products now support optional `warranty_info`.
- Central admin product create/edit forms can manage all three fields.
- Category defaults now suggest the matching product type in the create form.
- Product-type templates provide recommended specification labels for apparel,
  accessories, electronics, home and general products.
- Specification payloads are limited to flat label/value JSON objects with a
  maximum of 20 entries; nested data and oversized values are rejected.
- Product detail pages render structured specifications and warranty/support
  information when present.
- Existing fashion fields such as `fabric` remain available for Nous Telos and
  other apparel products.
- Products can now have database-backed variants for size, color, storage or
  any other option combination. Each variant has its own unique SKU, flat JSON
  attributes, optional price override, active status and stock quantity.
- The central admin can create or replace a product's variants through a
  validated JSON editor. Product summary stock is recalculated from active
  variants.
- Product detail pages render active options and update displayed price and
  stock when the customer changes selection.
- Cart lines use stable variant IDs while products without variants retain the
  previous size-text behaviour.
- Checkout locks and revalidates the selected variant, uses its current price,
  decrements only its stock, and stores SKU/label/attribute snapshots on the
  order item.
- Admin invoices, admin order details and customer receipts display the saved
  variant label.
- Products now carry structured delivery classes (`standard`, `fragile`, or
  `oversized`) and return rules (eligibility, return window and policy note).
- Checkout copies delivery and return metadata to each order item so historical
  orders retain the policy that applied when they were placed.
- Category pages, full search and brand storefronts now support general filters
  for product type, brand/category where applicable, delivery class, returns,
  stock and price. Search also checks SKU and structured specification content.
- Secondary product media now has an explicit role, accessible alt text and sort
  order. Supported roles are gallery, lifestyle, detail, packaging and size
  chart.
- Central admin can upload secondary media, edit its role/alt text/order and
  remove it. Product pages use media roles to separate gallery imagery from
  size charts.
- The legacy `ProductImage` model/schema field mismatch (`image_url` versus
  `image_path`) has been corrected.

## Compatibility

Existing products receive the safe `general` product type default and remain
publicly compatible. Products without variants continue to use product-level
price and stock. No existing product content was rewritten.

## Verification

- Migration `2026_09_20_140000_create_product_variants_and_add_order_item_snapshots`
  was applied successfully to the local database.
- Migration `2026_09_25_150000_add_catalog_operations_and_media_roles` was
  applied successfully to the local database.
- Variant add-to-cart requirements, independent stock limits, checkout stock
  decrement/snapshots and central-admin synchronization have feature coverage.
- `composer test` passes 98 PHP tests / 545 assertions and 10 frontend tests.
- `git diff --check` passes.

## Completion boundary

No planned Phase 2 catalog-foundation work remains. Future delivery surcharges,
carrier rules, return-request workflows, and advanced attribute faceting are
separate operational phases and require business rules before implementation.
