# Phase 1 — baseline and isolated test setup

Historical Phase 1 results below are preserved. Phases 2 and 3 are now complete;
see [Phase 2 shared UI](PHASE_2_SHARED_UI.md) and [Phase 3 catalog/vendor
integrity](PHASE_3_CATALOG_VENDOR.md) for current results. `composer test` now
also runs the passing frontend suite. Only Phase 4 backend regressions remain pending.

Completed 2026-09-18. Scope: test infrastructure and documentation only. No
storefront, controller, model, production migration/seeder, image or order-flow
behavior changed in this phase. Existing uncommitted vendor work was preserved.

## Owner constraints

- Earthquick is the marketplace; Nous Telos remains the detailed flagship vendor.
- Bright is an example electronics storefront for later development.
- Preserve the existing theme and implement incremental changes.
- One central admin only: no separate seller dashboards or management portals.
- Do not build commissions or payouts. Remove existing commission remnants in
  Phase 3, together with the related schema/form consistency cleanup.

## Baseline artifacts

Local, Git-ignored directory: `storage/qa/baseline-20260918-081415/`.

- `verified-baseline.zip`: pre-Phase-1 source, templates, styles, scripts and
  public assets, including previously untracked files; relative paths preserved.
- `files.json`: SHA-256 manifest of 224 original non-runtime files.
- `git-status.txt`, `working-tree.diff`: original dirty working-tree context.
- `database-before.json`, `database-after.json`: counts and fingerprints of
  vendors/categories/subcategories/products/images/users/orders/items/coupons.
  The fingerprints match. These contain no raw customer records or credentials.

The archive excludes `.env*`, dependencies, `.git`, runtime storage and scratch
files. It is a source/theme snapshot, not a full database backup or visual browser
snapshot. Do not overwrite current work from it indiscriminately. Artifacts stay
local and are intentionally not committed.

## Repeatable commands

From the project root:

```sh
composer test
composer test:regressions
composer test:frontend-regressions
```

Without Composer command availability:

```sh
php vendor/phpunit/phpunit/phpunit -c phpunit.xml
php vendor/phpunit/phpunit/phpunit -c phpunit.regressions.xml
node --test tests/Frontend/interactions.test.cjs
```

The default suite is green. At Phase 1 completion both explicit regression commands
exited nonzero. The frontend suite has since been fixed in Phase 2. Pending backend
tests encode unresolved issues rather than marking broken behavior as a
successful test. Run them during each relevant fix; move resolved backend tests
into the default suite as their phase is implemented. Do not mark them skipped
or invert their assertions simply to make the results green.

## Isolation design

`tests/bootstrap.php` establishes the test environment before application boot:

- Forces SQLite `:memory:` even if shell variables or `.env` select MySQL.
- Supplies a test-only application key and array sessions/cache/mail, sync queue.
- Redirects logs, compiled views, uploads and filesystem disks to a unique system
  temporary directory (`earthquick-tests-*`). Tests using `public_path()` cannot
  delete or replace original `public/images` files.
- Uses unique ignored `storage/qa/cache-*` paths for framework manifests and
  configuration/routes/events caches, avoiding live cached application settings.

`Tests\TestCase` refuses an unexpected environment/database, removes alternative
named database connections, migrates a fresh in-memory database per application,
and loads deterministic synthetic fixtures. It never runs DatabaseSeeder or
EarthquickSeeder and does not require the original local database to be running.

`tests/Support/CatalogFixture.php` provides two vendors, seven categories, six
subcategories and four synthetic Nous Telos products. Bright starts empty. No
real customer accounts, orders, passwords or database exports are fixtures.

Existing test methods were retained. Temporary run directories are retained for
debugging and can be cleaned up later after verifying their exact paths. This
phase does not introduce a broad automatic deletion command.

## Verified results

| Check | Result |
|---|---|
| Original suite on the new setup | 65 tests, 257 assertions passed |
| Default suite with new isolation/mixed-vendor checks | 67 tests, 280 assertions passed |
| Pending backend regressions | 11 tests, 18 assertions, 11 expected failures |
| Pending frontend mock-DOM regressions | 2 tests, 2 expected failures |
| Original business-table fingerprints after tests | Unchanged |

JUnit/output artifacts: `storage/qa/phase1-baseline.xml`, `phase1-green.xml`,
`phase1-pending.xml`, `pending-output.txt`, `frontend-pending-output.txt`.

SQLite verifies application flows but not MySQL-specific SQL, locking or
concurrency. Frontend checks use a mock DOM, not a browser. The preceding audit
could not connect to a browser; desktop/mobile visual verification is still
outstanding and must not be reported as passed.

## Regression ownership

| Phase | Pending checks / related issues |
|---|---|
| 2 — JS/shared UI | Reinitialization must not double-add; quick-view add must use the real cart. Also fix root-relative URL assumptions, competing card onclick handlers, duplicate toast ID and missing store main-content targets. These latter items still need browser/integration coverage. |
| 3 — catalog/vendor | Inactive vendor product visibility and cart add; category/subcategory consistency. Commission cleanup and destructive seeder ordering also belong here. |
| 4 — checkout/order | Out-of-stock/overstock add, updated price at checkout, stock decrement, vendor recheck, delivery consistency, guest receipt privacy, unverified contact order access. |

The pending tests currently use provisional response contracts (404 for hidden
resources, 422 for rejected cart input) and the existing advertised free-delivery
promise. Confirm the final shipping/price-change policy before Phase 4; adjust
the policy-specific expectations then, while retaining meaningful assertions
that display, calculation and authorization agree.

## Change boundaries for later phases

1. Stabilize shared JS and DOM contracts before moving navbar/card markup.
2. Preserve product/order vendor relationships while cleaning up admin fields.
3. Treat `home.blade.php` and `layouts/app.blade.php` separately for shared branding:
   home owns another HTML document. Both include navbar/footer.
4. Global card/grid styles and later responsive overrides affect home, category,
   search, vendor and related-product cards. Scope redesign rules accordingly.
5. Recheck both full cart/checkout pages and the JavaScript cart drawer when their
   JSON/session contract or displayed vendor/price/delivery data changes.

Phase 1 completion does not authorize automatically starting Phase 2 or fixing
the intentionally failing regressions within this phase.
