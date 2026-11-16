# Rthquick identity update — 2026-11-16

The owner selected Rthquick as the current platform and project identity. The
local rename work updated app metadata, Composer/npm package identity, runtime
mail/command/seeder identifiers, environment variable prefixes, customer-facing
logo references and the return-shipping payer value.

## Local database migration

- Migration: `2026_11_16_000000_rename_return_shipping_platform_value_to_rthquick`
- Status: applied to the configured local database on 2026-11-16.
- Before migration, a private 26-table database dump was written to the
  Git-ignored file
  `storage/qa/pre-rthquick-rename-20261116/earthquick_db-pre-rthquick-rename.sql`.
  The dump contains business data; do not commit or share it.
- The Laravel configuration cache was cleared and Composer optimized autoload
  files were refreshed after the migration.
- No production database migration was run.

## Legacy technical identifiers

The local XAMPP project directory and MySQL database keep their existing
`earthquick` names so the current local URL and database remain usable. Historical
return-payer values are accepted by the application for compatibility; the
migration normalizes them to `rthquick`.

Bright/Heaven Phase 10 remains a plan only and was not started as part of this
identity update. Automated tests and browser QA were not run in this slice.
