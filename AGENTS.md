# Rthquick project context

Read `PROJECT_MEMORY.md` and `project-record/README.md` before planning changes.
Use the current source as the authority for implementation status; phase documents
are historical records except where the project-record index marks work as planned
or in progress.

- Rthquick is the multivendor marketplace and owns the central customer/admin
  experience. There is one central admin; do not add seller dashboards, commissions,
  payouts or split fulfillment without a later explicit request.
- The two initial stores are Nous Telos (Bengali heritage/handloom) and Bright
  (electrical supplier). Preserve Nous Telos's rich presentation and Bright's
  wider electrical catalog scope.
- Phase 10 is active at the owner's request. Phase 1 (Bright/Heaven brand and
  warranty foundation) is complete; Phase 2 (Bright electrical catalog) is in
  progress. Bright is the store/seller, Heaven is its product brand, and Heaven
  is not a third store. Bright provides Heaven product warranties; do not invent
  warranty duration or terms.
- Make incremental changes to this Laravel/Blade application. Preserve existing
  product, order and vendor work and unrelated working-tree changes.
- Platform identity and policies must remain distinct from vendor-specific stories.
- Do not run tests unless the user asks for tests or verification. Do not run
  migrations or seeders against local/production business data without explicit
  user direction, a pre-migration backup and a reviewed migration plan.
- Keep historical database identifiers and migration filenames stable unless a
  reviewed migration plan explicitly changes them.

The owner requested Rthquick as the current project/platform identity. The local
XAMPP directory/database may retain legacy technical names until separately
migrated; do not rename those paths or databases implicitly.
