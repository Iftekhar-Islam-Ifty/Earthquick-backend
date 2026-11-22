# Bright / Nous Telos Phase 9 - Customer journey readiness

**Status: Implementation complete; owner visual review pending.** Started
2026-11-20 at the owner's request after the Bright hero and catalog onboarding
preparation were committed.

## Goal

Keep marketplace-level navigation neutral, and preserve store context from
store selection through catalog and product discovery. Prepare the customer
journey for final browser review without mixing the two stores' catalogs.

## Scope

- Audit entry links, store directory, store homepages, catalogs and category links.
- Keep generic marketplace links from silently entering a vendor-specific catalog.
- Preserve the existing shared Rthquick cart and checkout flow.
- Do not add seller dashboards, commissions, split fulfillment or unverified
  Bright product and warranty claims.

## Delivered

- Changed the legacy `/shop` entry redirect to the neutral store directory
  (`/stores`). It previously sent every top-level Shop click to the Nous Telos
  Women's catalog, which did not represent Bright or the marketplace as a whole.
- Category-specific `/shop/{category}` and `/shop/{category}/{subcategory}`
  routes remain available for direct category links.
- Nous Telos catalog now keeps the full active store taxonomy visible, including
  categories with no current stock, and renders each category's active
  subcategories in a separate wrapped row. Parent category and subcategory links
  preserve a matching catalog filter; navbar links such as Kantha and Bedsheet
  resolve to their Home Decor parent and exact subcategory. Empty categories show
  an accurate empty result instead of being hidden or populated with demo data.
- Nous Telos and Bright mobile catalog headers now use a compact horizontal
  logo/name/count layout, hiding secondary store copy and the vendor code on
  narrow screens. The catalog section's oversized top and bottom spacing is
  reduced on mobile.
- Product detail breadcrumbs and related-product links now return to the
  product's own store catalog/category. The product page also links its store
  identity back to that store homepage, keeping store context through discovery.
- Audited the cart, checkout and receipt paths: item-level store, product-brand
  and warranty-provider labels are retained in session/cart display, checkout
  summary and order-item receipt snapshots. The shared Rthquick cart/checkout
  remains central for both stores.

## Remaining

- Owner is reviewing desktop/tablet/mobile behavior. Apply any reported visual
  fixes and close the phase after that review.

No migrations, seeders or business records are part of this phase.
