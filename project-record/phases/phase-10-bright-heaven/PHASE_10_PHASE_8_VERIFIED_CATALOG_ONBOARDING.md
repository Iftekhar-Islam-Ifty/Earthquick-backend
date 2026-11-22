# Bright / Nous Telos Phase 8 - Verified catalog onboarding

**Status: In progress.** Started 2026-11-20 at the owner's request. This phase
consolidates the previously prepared Phase 5 catalog-intake work so there is one
active onboarding track.

## Goal

Prepare and enter Bright catalog items only from seller-verified information.
Bright remains the store/seller; Heaven is selected as product brand only when
Bright confirms that attribution. Do not invent product, stock, price, source,
return or warranty claims.

## Work completed

- Consolidated the Phase 5 intake checklist/template into this phase; Phase 5 is
  now a historical preparation record, not a parallel active phase.
- Reviewed the existing central admin product create/edit flow. It already
  supports store, product brand, category, price, stock, specifications,
  warranty/support text, return policy, delivery class, images and variants.
- Replaced the admin warranty example that implied a one-year manufacturer
  warranty with neutral seller-confirmed wording and a Bright/Heaven-specific
  reminder in both create and edit forms.
- Kept the existing CSV intake template as a collection aid; it does not import,
  publish or modify catalog records.
- Added a three-slide hero to Bright's main store homepage (not the catalog),
  using clearly illustrative electrical demo artwork and the existing carousel
  controls/autoplay behavior. The
  images are replaceable at `public/images/bright-demo/hero-*.svg`.
- Removed the repeated large Bright intro banner below the hero; the first hero
  slide now carries the company identity and the homepage proceeds to categories.
- Set the source artwork canvas to **1920 x 800 px** (2.4:1). Use the same ratio
  for replacement JPEG/WebP images; keep important subject matter toward the
  center/right because the left side carries text and mobile crops differ.
- Added a Bright-specific store information and policy page, linked from the
  Bright homepage and store footer. It separates Bright as seller from Heaven as
  product brand, points delivery/returns/refunds to Rthquick's central policy,
  and avoids inventing product-specific warranty terms.
- Audited policy copy across Bright, Rthquick, product, cart and order pages.
  Removed unsupported delivery-time promises and repeated fee tables from
  summaries; the Rthquick policy page remains the single source for delivery,
  cancellation, return and refund rules, while each product/order shows its
  applicable item-specific details. Legacy order records without a saved return
  window now direct customers to support instead of displaying an empty term.

## Bright inputs still required

- Product name, category/subcategory, product type and confirmed brand.
- Current price and stock with confirmation date.
- Accurate description/specifications and product images.
- Warranty responsibility and exact terms; leave terms empty until confirmed.
- Bright should confirm the wording and any approved product-specific warranty
  coverage, period and exclusions before those details are added to listings.
- Return eligibility/window/notes, delivery class and any actual variants.

The original source checklist is
[Bright catalog intake template](../../reference/BRIGHT_CATALOG_INTAKE_TEMPLATE.csv).

## Remaining

- Receive Bright-verified catalog data, then enter it through the central admin.
- Complete Phase 7 browser review when local browser access is available. This
  Phase 8 preparation can proceed independently, but launch review remains open.

Bright's verified product and warranty details are an external input; the
catalog cannot be truthfully completed until Bright supplies them. No demo
product records or warranty terms were fabricated.

No product records, migrations, seeders or business data were changed.
