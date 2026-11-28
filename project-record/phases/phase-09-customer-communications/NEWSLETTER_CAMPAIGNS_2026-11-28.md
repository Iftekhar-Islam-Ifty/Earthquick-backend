# Nous Telos newsletter campaigns — 2026-11-28

## Scope

- Central admin can save and edit drafts, preview the saved content and launch
  a campaign deliberately. No campaign is launched by deployment or migration.
- Launch snapshots confirmed, active Nous Telos subscriptions. A five-minute
  scheduled command enqueues at most five recipient messages each run.
- Every message uses the configured NOC SMTP identity and a signed unsubscribe
  link. The sender and recipient eligibility are checked again before SMTP
  handoff, including outbox retries after an unsubscribe.
- Admin can stop remaining emails after launch. SMTP-accepted messages cannot
  be recalled.
- Per-recipient outbox records preserve retry/failure visibility. SMTP accepted
  is not proof of inbox delivery. Hosting hourly mail limits should be checked
  before launching a large campaign.

## Migration review

`2026_11_28_130000_create_newsletter_campaigns_table.php` only adds
`newsletter_campaigns` and `newsletter_campaign_recipients`. It does not alter
orders, users, subscribers or the existing mail outbox. Back up and verify the
local or production database before migration. Run `php artisan migrate --force`
in the deployed Laravel project and inspect `php artisan migrate:status`.
Rollback drops campaigns and their recipient audit, so use a forward fix once
real campaigns exist.

Local backup: `storage/app/backups/earthquick_db-before-newsletter-campaigns-2026-11-28.sql`
(28 table definitions checked). The first attempt failed on a MySQL foreign-key
name length limit. Both newly created tables had zero rows, were removed, and
then migrated successfully with short explicit constraint names in batch 30.

The separate signup migration must also be deployed on cPanel. Local XAMPP
still has a Gmail sender and cannot launch or send this NOC-only feature until
its private SMTP settings are updated. Do not commit mailbox passwords.
