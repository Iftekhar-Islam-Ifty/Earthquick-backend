# Phase 10 - Bright store and Heaven product brand

**Status: Phase 5 intake preparation consolidated into Phase 8; Phase 6 implemented locally; Phase 7 implementation delivered with browser review pending; Phase 8 verified catalog onboarding in progress.** Bright-verified catalog data and browser review remain before launch.
The broader work was recorded 2026-11-16 from the owner's direction.

## Owner-approved identity structure

```text
Rthquick - marketplace and central customer care
|- Nous Telos - store
|- Bright - electrical products store/business
   |- Heaven - Bright-owned product brand
```

There are two stores: Nous Telos and Bright. Heaven is a product brand under
Bright, not a third store. Bright owns and controls Heaven and provides its
product warranties. Confirm warranty duration/terms, manufacturer or importer
attribution, invoice identity and after-sales responsibilities before launch.
Do not invent product or warranty claims.

## Intended customer presentation

- Bright's store page presents Bright Electrical Company as the seller/store.
- Heaven-branded products show `Brand: Heaven` and `Store: Bright` separately.
- Example: **Heaven 2-Gang Switch** - Brand: Heaven - Sold by: Bright Electrical
  Company - Warranty provided by Bright (show only verified duration/terms).
- Store directory contains Nous Telos and Bright only. Heaven may have a brand
  collection/filter inside Bright, but no independent store page.
- Bright's catalog may cover switches, sockets, LED lights, circuit breakers,
  fans and other verified electrical products; it is not restricted to lighting.

## Application structure

1. Keep the existing central-admin model and current store/vendor records.
2. Add a first-class product-brand relationship separate from the selling store.
   Initially Heaven belongs to Bright; leave the model extensible for future
   brands and sellers without creating seller dashboards.
3. Keep product brand optional for legacy records and preserve existing variants,
   specifications, warranty, return, delivery and stock behavior.
4. Extend central product administration to select Store and Brand separately.
5. Present brand, seller and warranty provider separately on product pages, cart,
   checkout, order snapshots, invoices and customer/admin order views.
6. Keep catalog categories shared and add only verified electrical categories
   and product data.
7. Preserve Rthquick as marketplace identity and its central support, order,
   cancellation, return and refund responsibilities.

## Delivery sequence

1. [Phase 1 foundation](../phases/phase-10-bright-heaven/PHASE_10_PHASE_1_BRAND_FOUNDATION.md): brand/store/warranty relationships, admin product selection, customer labels and order snapshots.
2. [Phase 2 catalog foundation](../phases/phase-10-bright-heaven/PHASE_10_PHASE_2_ELECTRICAL_CATALOG.md): implement Bright catalog/category setup and demo listings. Real commercial product data remains subject to Bright verification.
3. [Phase 3 product experience](../phases/phase-10-bright-heaven/PHASE_10_PHASE_3_PRODUCT_EXPERIENCE.md): refine storefront/search and product presentation without inventing product claims.
4. [Phase 4 integration verification](../phases/phase-10-bright-heaven/PHASE_10_PHASE_4_INTEGRATION_VERIFICATION.md): verify Bright/Heaven identity through product, cart, checkout and receipt; keep the full suites green.
5. [Phase 5 intake preparation](../phases/phase-10-bright-heaven/PHASE_10_PHASE_5_CATALOG_ONBOARDING.md): historical preparation; follow-up work is consolidated in Phase 8.
6. [Phase 6 store/catalog separation](../phases/phase-10-bright-heaven/PHASE_10_PHASE_6_STORE_HOME_CATALOG_SEPARATION.md): split the marketplace entry, each store homepage and each store catalog into clear customer journeys.
7. [Phase 7 responsive store journeys](../phases/phase-10-bright-heaven/PHASE_10_PHASE_7_RESPONSIVE_STORE_JOURNEYS.md): refine desktop, tablet and mobile menu/catalog behavior without mixing store catalogs.
8. [Phase 8 verified catalog onboarding](../phases/phase-10-bright-heaven/PHASE_10_PHASE_8_VERIFIED_CATALOG_ONBOARDING.md): enter Bright-confirmed products, prices, stock, images, policies and exact warranty terms in the central admin.
9. [Phase 9 customer journey readiness](../phases/phase-10-bright-heaven/PHASE_10_PHASE_9_CUSTOMER_JOURNEY_READINESS.md): audit neutral marketplace entry and store-scoped browsing through cart/checkout; consolidate final journey fixes after owner browser review.
10. Browser review of desktop/mobile product, cart, checkout, receipt and after-sales flows remains a release check. Phase 7/8 browser review is currently with the owner.

## Explicit boundaries

- No separate Bright seller portal, commissions, payouts or split fulfillment.
- No fabricated product list, manufacturer, certification, warranty, or claim
  that Heaven has a third-party partnership.
- Rthquick is the current platform identity; local XAMPP/database legacy names
  remain technical identifiers until separately migrated.
