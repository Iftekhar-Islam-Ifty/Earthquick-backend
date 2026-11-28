# Nous Telos newsletter signup — 2026-11-28

The previous form only displayed a success toast and discarded the address.
This increment adds an opt-in list for Nous Telos. The later admin-initiated
campaign workflow is documented in `NEWSLETTER_CAMPAIGNS_2026-11-28.md`.

## Migration review

- Add one `newsletter_subscriptions` table. No existing table or record changes.
- A unique `(store_slug, email)` key prevents duplicate subscriptions. Only a
  SHA-256 hash of the random confirmation/unsubscribe token is stored.
- Before applying locally or on cPanel, take and verify a database backup.
- Local backup: `storage/app/backups/earthquick_db-before-newsletter-2026-11-28.sql`
  (27 table definitions checked). The first local attempt failed on MySQL's
  index-name length limit. Its newly created table had zero rows, so it was
  removed; a shorter explicit index name then migrated successfully in batch 29.
- Apply `php artisan migrate --force` in the deployed Laravel project. Confirm
  `php artisan migrate:status` marks the new table migration as Ran.
- To roll back only this batch, back up first and use `php artisan migrate:rollback
  --step=1` only if it contains this migration alone. Rollback deletes subscriber
  records, so prefer a forward fix after any real subscriptions exist.

## Flow

1. The visitor submits an email on the Nous Telos homepage. A throttled server
   endpoint stores a pending signup and sends a confirmation email from the
   configured Rthquick sender. The form does not claim the address is active yet.
2. The visitor opens the link and explicitly confirms. A welcome email with an
   unsubscribe link follows. The configured support address (`noc@rthquick.com`
   on production) receives a new-subscriber notification.
3. Unsubscribe requires a deliberate form submission so automated mail-link
   scanners do not remove subscribers. No future marketing messages should be
   sent to unconfirmed or unsubscribed addresses.

The production NOC SMTP settings and transactional notification switch were
already configured in cPanel. A production code pull and migration are still
needed for this feature to operate there. The local XAMPP sender currently
remains its prior Gmail account, so newsletter signup correctly reports
unavailable there until local SMTP is changed to the NOC sender. Never copy
the NOC mailbox password into git.
