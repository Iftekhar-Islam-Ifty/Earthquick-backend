# Phase 9 — Customer Communications and Support

Status: Local SMTP smoke test reached the owner's inbox and local notifications were enabled on 2026-09-28. A durable email outbox, retry command, health check and central-admin delivery screen are implemented locally. Production cron/migration/operational sign-off are not yet verified. The owner deferred automated SMS.

## Implemented

- Replaced the About-page demo contact form with a CSRF-protected, validated POST endpoint. A valid inquiry is saved in `support_inquiries` before a success message is shown. A hidden spam field and per-IP request throttling reduce automated submissions. An email address is optional; a valid Bangladeshi phone number is required.
- Added a central-admin Support Inbox with status filters, pagination, an internal follow-up note, actor and timestamp. Status changes do **not** pretend to send a customer reply. Guest/customer access to the inbox is denied.
- The inbox now has manual call and email links for the customer's saved contact details. Clicking them opens the admin's dialer/email app; saving a follow-up note still does not contact the customer.
- Removed the hard-coded personal email/phone from About and the shared footer. `EARTHQUICK_SUPPORT_EMAIL` and `EARTHQUICK_SUPPORT_PHONE` populate the public contact details when the owner supplies them; otherwise the site directs visitors to the saved support form. The About return FAQ no longer promises an automatic courier pickup or a blanket return window.
- Added customer-facing transactional email hooks for order placement, lifecycle status changes, COD payment collection, cancellation request/decision, return request/authorization/receipt/inspection, and refund approval/completion. Support inquiry acknowledgement and admin alert are also prepared. Messages use the order's saved customer email, falling back to the linked account email if present. Phone-only/guest orders with no email do not receive email.
- Email delivery is **off by default** (`EARTHQUICK_EMAIL_NOTIFICATIONS_ENABLED=false`). A `log`, `array` or `failover` mailer does not qualify as delivery. With a real transport enabled, each transactional notice is saved to an outbox before an immediate SMTP attempt. Failures do not reverse the committed order/inquiry. Pending notices retry after 5, 15, 60 and 360 minutes when the scheduler runs. After five failed attempts they require central-admin review and an explicit manual retry. The outbox encrypts recipient, subject and body while pending, then clears those fields after SMTP handoff. The masked recipient and event metadata remain for operational visibility. SMTP acceptance is not an inbox delivery receipt; if a worker dies immediately after SMTP accepts a message, retrying its stale claim can produce a duplicate notice.
- Public email and phone are clickable `mailto:` and `tel:` actions, not automatic replies/calls. The owner has now confirmed WhatsApp is active on the support phone, so the local `.env` sets `EARTHQUICK_SUPPORT_WHATSAPP=8801793127287` and the About/footer chat links are visible. This is a click-to-chat link, not a site-hosted live-chat inbox or bot; customers must press Send in WhatsApp themselves.
- `php artisan earthquick:mail-test recipient@example.com` provides one controlled transport test, without creating an order or inquiry. It refuses `log`/`array`/`failover` and the example sender. A successful SMTP handoff still requires checking actual inbox delivery.
- After the owner privately configured Gmail SMTP in the local `.env`, an initial sandboxed SMTP attempt failed because outbound port 587 was blocked there. An approved real-network check connected to port 587, and the mail-test command handed off one test message successfully. The owner confirmed receipt in Gmail. Local `EARTHQUICK_EMAIL_NOTIFICATIONS_ENABLED` was then set to `true`, and `php artisan config:cache` confirmed `communications.email_enabled=true` with `mail.default=smtp`. No password is recorded here. This is **not** proof of production delivery, ongoing deliverability, or every event-specific template; the full automated suite covers those hooks with a fake mailer.
- The migration `2026_09_27_180000_create_support_inquiries_table.php` ran only against the local `earthquick_db` after a verified 22-table private backup at `storage/qa/phase9-db-backup/earthquick_db_pre_phase9_2026-09-28.sql`. The ignored backup contains private data; do not commit or share it. No real inquiry was submitted by the implementation.
- In an isolated SQLite browser session, Playwright checked the contact page and admin Support Inbox at 1440px, 390px and 320px, submitted one synthetic phone-only inquiry, and confirmed it appeared in the inbox. All six pages returned 200 with no document-level overflow, duplicate IDs or JavaScript page errors. Full-page screenshots and `report.json` are ignored under `storage/qa/phase9-browser/`. The local business database was not used for browser submissions.
- Before the outbox change, `composer test` passed 144 PHP tests / 936 assertions and 10 JavaScript tests. With the outbox change it passes 158 PHP tests / 1126 assertions and 10 JavaScript tests (2026-10-01). Automated email tests use Laravel's fake mailer; they do not claim production SMTP delivery. The earlier live About/homepage checks returned 200 and displayed the configured phone/email/WhatsApp link.
- After WhatsApp confirmation, Playwright rendered About at 1440px, 390px and 320px and clicked its chat link. Each opened a new tab targeting `https://wa.me/8801793127287`; the external request was intercepted for QA, so no message was sent. The homepage footer also rendered the same link. This checks website link behavior, not a real incoming WhatsApp message or reply time.

## Activation when the owner has real email/contact details

1. Choose and verify an actual sending mailbox/domain with an SMTP provider. Set `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_SCHEME`, `MAIL_USERNAME`, `MAIL_PASSWORD`, and `MAIL_FROM_ADDRESS`/`MAIL_FROM_NAME` in the private production `.env`. Keep credentials out of Git. Do not use the example sender or `log` mailer for production delivery.
2. Optionally set `EARTHQUICK_SUPPORT_EMAIL` (admin alerts and public contact address) and `EARTHQUICK_SUPPORT_PHONE` (public contact number). These values do not have to equal the SMTP sender. The support inbox remains functional if either is unset.
3. Test with a controlled mailbox using a disposable inquiry and an isolated order before enabling customer notifications. Then set `EARTHQUICK_EMAIL_NOTIFICATIONS_ENABLED=true`, refresh cached configuration (`php artisan config:cache` after reviewing production settings), and repeat the send/receive check, including spam-folder and sender authentication checks. Do not use a real customer order as a test fixture.
4. Monitor `php artisan earthquick:mail-health` and Admin > Customer Care > Email Deliveries. A pending notice needs the scheduler; a failed notice needs staff review before pressing Retry. Neither a successful order nor an SMTP handoff proves inbox delivery.

## Production rollout and sign-off still required

1. Back up the production database first. Deploy code, then run `php artisan migrate --force` in the Laravel project directory to create `outbound_messages`. Do not open the new admin Email Deliveries page before the migration; it depends on that table. Keep the existing production `APP_KEY` unchanged so encrypted pending messages remain readable. No production migration was run from this workspace.
2. Confirm the private production `.env` uses the intended real SMTP sender and `EARTHQUICK_EMAIL_NOTIFICATIONS_ENABLED=true`, then refresh Laravel's config cache if needed. Do not paste the SMTP password into logs, support messages, screenshots or Git.
3. Run `php artisan earthquick:mail-health` (read-only) and `php artisan schedule:list`. Configure cPanel Cron Jobs to run `php artisan schedule:run` every minute, using the server's actual PHP executable and absolute Laravel project path. Check `which php` and `pwd` in that project first; do not assume the interactive PHP path is the cron PHP path. The scheduled outbox retry checks due messages every five minutes. A one-time manual run of `php artisan earthquick:mail-retry` is available after transport is confirmed.
4. Use only controlled test addresses: send a mail smoke test and a disposable support inquiry, then verify both expected emails actually arrive (including Spam) and the admin Email Deliveries entries become `sent`. Simulate a failure only in an isolated staging/test environment, never by disrupting live customer mail. Confirm admin-only access and responsive display on the production site. Review failed entries and contact the customer manually if time-sensitive. Record the date, tester, delivered event types and cron observation before calling Phase 9 production-signed-off.

Automated SMS is deliberately **deferred by owner decision**. Public `tel:` and confirmed WhatsApp click-to-chat links remain manual contact paths; neither sends automated SMS. A later SMS phase requires a provider, cost/consent policy and separate integration approval.

Adding a phone number displays a call option; it does **not** send SMS or WhatsApp messages. WhatsApp click-to-chat opens an external conversation without a provider integration, but automated messages would require a chosen provider, credentials, consent/cost policy and separate integration/testing. bKash remains Coming Soon. The homepage marketing newsletter is also separate from transactional email and is not activated by this switch.

## What each contact path actually does

| Path | When to use it | What happens |
| --- | --- | --- |
| Support form | A customer needs a tracked inquiry | Saved in Earthquick's central admin inbox; optional acknowledgement/admin-alert emails only after SMTP is activated. This is not a live chat. |
| Email link | Detailed, non-urgent correspondence | Opens the visitor's own email app, addressed to the configured support mailbox. Staff reply manually. Transactional order emails are a separate outgoing SMTP workflow. |
| Call link | Urgent issue needing a conversation | Opens the visitor's dialer; the call must be placed and answered by people. No automated call or SMS. |
| WhatsApp link | Quick manual chat, **only after the number is confirmed** | Opens WhatsApp. The website does not receive/store that chat and cannot guarantee availability or response time. |

## Example private `.env` setup for Gmail SMTP

This is a template, **not** a verified live configuration. Replace the password placeholder in the private `.env`, never in `.env.example`, a commit, or a chat message. A Gmail app password (if available for the account) is different from the normal login password and requires 2-Step Verification. Google may not offer app passwords for every account. A dedicated transactional sender is preferable for production volume.

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_SCHEME=null
MAIL_TIMEOUT=10
MAIL_USERNAME=your-address@gmail.com
MAIL_PASSWORD="YOUR_GOOGLE_APP_PASSWORD"
MAIL_FROM_ADDRESS="your-address@gmail.com"
MAIL_FROM_NAME="Rthquick"

EARTHQUICK_SUPPORT_EMAIL=your-address@gmail.com
EARTHQUICK_SUPPORT_PHONE=01XXXXXXXXX
EARTHQUICK_SUPPORT_WHATSAPP=
EARTHQUICK_EMAIL_NOTIFICATIONS_ENABLED=false
```

Then run `php artisan config:cache` and `php artisan earthquick:mail-test your-controlled-inbox@example.com`. Check that mailbox (and spam folder). Only after it arrives, change `EARTHQUICK_EMAIL_NOTIFICATIONS_ENABLED=true`, run `php artisan config:cache` again, and submit one disposable support inquiry with a different controlled email to verify both the admin alert and customer acknowledgement. Do not create a real order to test mail. If SMTP delivery is not working, restore the switch to `false` and recache. Keep WhatsApp blank until the number is confirmed active; when confirmed, enter digits in international format, e.g. `8801XXXXXXXXX`, then recache. Neither the support phone nor WhatsApp link sends an automated notification.
