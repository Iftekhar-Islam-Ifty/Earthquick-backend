# Bright Phase 5 - Verified catalog onboarding

**Status: Consolidated into Phase 8.** This record documents the original
2026-11-17 intake preparation. Follow-up onboarding work is tracked in
[Phase 8](PHASE_10_PHASE_8_VERIFIED_CATALOG_ONBOARDING.md).

## Goal

Prepare to replace Phase 2 demo listings with Bright-verified catalog records.
Bright remains the selling store; Heaven is selected only for products Bright
confirms belong to that brand. Do not create product claims or warranty terms.

## Started

- Added [Bright catalog intake template](../../reference/BRIGHT_CATALOG_INTAKE_TEMPLATE.csv)
  matching fields currently supported by the central product form.
- Documented which commercial facts Bright must confirm before an admin enters a
  product. This is an intake aid only; it does not import or publish records.

## Required Bright inputs

- Product name, category/subcategory, product type and exact product brand.
- Current selling price and available stock, with the date Bright confirms them.
- Product description and factual specifications from Bright or its supplier.
- Exact warranty wording and Bright's confirmation of warranty responsibility;
  leave warranty blank until confirmed.
- Main product photo and optional hover/gallery images supplied for publication.
- Return eligibility, window and notes, plus delivery handling class.
- Variant labels, prices and stock where the actual product has selectable options.

## Boundaries and next work

- Enter products through the central admin after Bright supplies verified inputs.
- Keep Bright as seller/store and show Heaven as product brand where confirmed.
- Do not create a Heaven store page, manufacturer/partnership claims, or guessed
  warranty duration.
- Do not replace demo records, edit business data, run migrations or seeders as
  part of the intake preparation.
- Complete desktop/mobile browser review when the browser capture environment is
  available; this remains a launch check.
