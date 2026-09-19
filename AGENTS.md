# Earthquick project context

Read `PROJECT_MEMORY.md` before planning changes. It records the owner's intent,
the existing architecture, and gaps found during the 2026-09-18 source review.

- Earthquick is the multivendor marketplace/platform.
- Nous Telos is its primary initial vendor: a real Bengali heritage/handloom shop,
  comparable in category to Aarong. Preserve its rich presentation and priority.
- Bright is a second vendor, an electronics shop; initially it is a demonstration
  storefront to develop properly later. Do not assume it is limited to lighting.
- More independent vendors and product categories will be added over time.
- Current scope: one central Earthquick admin. Do not build separate seller
  dashboards or management portals. Do not add commissions or payouts; existing
  commission form/controller remnants are to be removed in Phase 3.
- Phases 1, 2 and 3 are authorized and implemented. Phase 2 fixes shared frontend
  URLs, event binding, quick-view/cart actions and shared DOM targets. Phase 3 adds
  public catalog vendor-availability rules, catalog hierarchy validation, removes
  vendor commission/featured remnants, and fixes development seed ordering.
  `composer test` runs the passing PHP and JavaScript suites. Backend fixes remain
  pending in `composer test:regressions`; do not start Phase 4 without user direction.
  Read `docs/PHASE_1_BASELINE.md`, `docs/PHASE_2_SHARED_UI.md` and
  `docs/PHASE_3_CATALOG_VENDOR.md` for test setup/status.
- Prefer incremental changes to this Laravel/Blade application; the owner does
  not want a full rewrite or removal of the existing Nous Telos work.
- Platform branding and policies should be distinct from vendor-specific stories.
- Existing vendor work was already modified/untracked at the review's start.
  Preserve unrelated working-tree changes.
- The 2026-09-18 request authorized analysis and repository memory, not a feature
  implementation. Future implementation scope comes from subsequent requests.

Use source code as the authority for implementation status. The older deployment
and architecture guides contain outdated Laravel/version and brand descriptions.
