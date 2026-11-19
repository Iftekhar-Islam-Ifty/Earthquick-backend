# Bright / Nous Telos Phase 7 - Responsive store journeys

**Status: In progress.** Started 2026-11-19 at the owner's request after
Phase 6 separated the marketplace, store homepages and store catalogs.

## Goal

Keep the Rthquick, Nous Telos and Bright browsing journeys clear and usable on
desktop, tablet and mobile. Store selection, category navigation, catalog
search, sorting and filters should remain within their intended store context.

## Scope

- Check navigation behavior across desktop hover, tablet touch and mobile drawer.
- Keep the catalog controls aligned at desktop sizes and prevent category chips
  from requiring horizontal scrolling on narrow screens.
- Preserve separate store catalogs and shared Rthquick cart/checkout flows.
- Review accessible toggle state and keyboard dismissal for menu interactions.
- Do not rely on unverified Bright product or warranty claims.

## Started / delivered

- Catalog search now shares a desktop row with that store's category options;
  mobile category chips wrap instead of scrolling sideways.
- Tablet Shop/Stores dropdowns (769–991px) can open from the tap-controlled
  expanded state and close with Escape or a click outside the menu.
- Bright Electrical keeps the same collapsed mobile accordion behavior as the
  other Shop groups.

## Remaining

- Browser visual review at desktop, tablet and mobile sizes is still pending;
  local browser access is unavailable in this chat.
- Confirm the final browsing flows in a live browser before marking this phase
  complete.
- Bright's verified catalog and warranty inputs remain a separate Phase 5
  launch dependency.

## Checks

- `node --check public/js/script.js` and `git diff --check` pass.
- `node --test tests/Frontend/interactions.test.cjs`: 12 passed.
- `tests/Feature/SharedUiTest.php`: passed.
- Existing storefront/homepage feature tests still assert the pre-Phase-6
  combined homepage and legacy `/stores/{slug}?filters` URLs. Those assertions
  need a separate update to the approved store-home/catalog journey; their
  failures do not establish a regression in the Phase 7 tablet-menu changes.

No database, migration, seeder or business-record changes are part of this phase.
