# Bright Phase 3 - Product and search experience

**Status: Local implementation complete; browser review and Bright-verified
catalog data remain before launch.** Started 2026-11-17 after completing the
Phase 2 catalog implementation locally; completed locally 2026-11-17.

## Goal

Present Bright/Heaven products correctly across storefront search, category cards,
quick view and product details while preserving Nous Telos's heritage presentation.
Use only product descriptions, specifications, ratings and warranty terms that
exist in the product record or have been verified by the store. The Phase 2 demo
records are for interface review only.

## First implementation slice - 2026-11-17

- Replaced fashion-only fallback copy in search and empty-cart states with
  marketplace-wide product/store language.
- Search cards and live suggestions distinguish product category, product brand
  and selling store; removed the false Handloom fallback for products without a
  fabric value.
- Product pages no longer invent an artisan short description or a default
  4.9-star / 48-review claim when the record has no short description or reviews.
- Quick view uses the product's own short description and optional brand/store
  identity; removed hard-coded loom origin, garment care and apparel sizing claims.
- Product recommendations are restricted to the same store and category, and
  Bright product pages link back to Bright's store.
- Cart and checkout no longer show a generic `Standard` choice for products that
  have no selected variant.
- Category/search banners and empty states use marketplace-wide language rather
  than implying all products belong to Nous Telos.
- Empty cart and empty order-history prompts direct customers to the store directory.
- Generic product usage guidance no longer makes unsupported apparel-care or
  electrical compatibility claims.
- Customer receipts and admin order status labels use general marketplace terms.
- Return request wording no longer assumes the item is apparel.

No migration or seeder was needed. This slice changes presentation/query code
only; no product, order or warranty records were changed.

## Launch checks outside the local implementation

1. Replace Phase 2 demo records with Bright-verified Heaven/Bright photos,
   specifications, prices, stock and warranty terms before launch.
2. Review desktop and mobile layouts in a browser. This environment had no
   active visual review session during this implementation pass.

Automated tests were not run under the current repository instructions. No
migration or seeder was needed; changes are presentation/query/documentation only.
