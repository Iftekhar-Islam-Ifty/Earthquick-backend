# Rthquick Phase 11 - Security hardening plan

**Status: In progress.** Local application controls and the MFA migration are
implemented; production configuration and deployment verification remain. This mini-phase follows the
source-level security review requested on 2026-11-24. The review did not include
live cPanel inspection or penetration testing.

## Goal

Close the highest-impact application and deployment gaps while preserving the
single central-admin model and the existing customer/store flows.

## Phase 11.1 - Production and session baseline

- Confirm the web document root exposes only Laravel's `public/` directory;
  keep `.env`, source, logs and backups outside the public root.
- Set production `APP_ENV`, `APP_DEBUG=false`, a unique `APP_KEY`, HTTPS and
  `SESSION_SECURE_COOKIE=true`; preserve HttpOnly and SameSite protections.
- Add CSP in report-only mode first; enable HSTS only after the production
  domain is confirmed HTTPS-only. Account for current inline scripts/styles.
  (Report-only header implemented locally; browser report review remains.)
- Verify the deployed configuration and response headers without exposing
  secrets or changing live hosting settings until the owner authorizes it.

## Phase 11.2 - Accounts and abuse controls

- Repair registration throttling so each attempt is counted and successful
  registrations cannot reset the IP limit; set the minimum password length to
  at least 12 characters.
- Add appropriate throttles to guest order creation and public search endpoints.
- Make admin authentication browser-session-only: ignore Remember me for admins,
  expire the admin session on browser close, and require login again after a
  short inactivity timeout. Keep admin authenticated while viewing storefront
  pages in the same browser; count inactivity from admin requests. Keep customer
  Remember me/session behavior unchanged; revoke any old admin remember token
  when this policy first takes effect.
- Add MFA for the central admin, verified-email password recovery, and recent
  authentication for high-impact actions where practical.
- Remove `is_admin` from general mass-assignment and keep admin provisioning on
  an explicit trusted path. Preserve the one-admin design; add no seller roles.
  (Implemented locally on 2026-11-25.)

## Phase 11.3 - Dependency and release review

- Run Composer and npm advisory audits when network access is available; review
  and update affected dependencies without unreviewed major-version upgrades.
  (Completed locally on 2026-11-25; both lockfile audits are clear.)
- Check the finished deployment for public `.env`/debug exposure, session cookie
  flags, admin access boundaries, throttling behavior, and security headers.
- Record remaining accepted risks and any cPanel actions the owner must perform.

## After Phase 11 - Laravel 13 / PHP 8.3+ readiness handoff

When Phase 11 is complete, review these upgrade prerequisites with the owner
before starting the Laravel 13 upgrade:

- Local XAMPP PHP version and extensions versus cPanel PHP version and extensions.
- Composer platform requirements, locked package compatibility, and package audit.
- cPanel document root, PHP handler, cron/queue setup, writable storage paths,
  and production environment settings.
- MySQL version, backups/rollback plan, storage links/uploads, mail configuration,
  and any Laravel 13 code or configuration changes.
- Run the upgrade first on an isolated staging copy and check checkout, admin,
  image uploads, sessions, and deployment before production rollout.

Remind the owner of this checklist at Phase 11 closure; do not start the Laravel
13 upgrade automatically as part of this security phase.

## Completion criteria

- Public requests cannot retrieve application secrets or detailed debug traces.
- Production session cookies are HTTPS-only, HttpOnly and SameSite protected.
- Registration and anonymous high-volume endpoints enforce effective limits.
- Admin access has MFA and privileged role assignment remains server-controlled.
- Dependency audit results and deployed security-header checks are recorded.

## First implementation increment - 2026-11-24

- Registration requests are counted before validation (five attempts per IP per
  10 minutes), successful registration no longer clears the IP limit, and new
  passwords require 12 characters.
- Guest checkout submission is limited to 10 requests per minute per IP;
  search and live suggestions are limited to 60 requests per minute per IP.
- Admin login ignores Remember me, rotates any existing remember token during
  password sign-in, uses a browser-session cookie while in the admin route
  group and public storefront pages, and expires after 15 minutes without an
  admin request. Viewing the storefront does not end the admin session or reset
  its idle clock. Pre-change admin sessions are rejected once and require a
  fresh password login. Customer remember-me remains unchanged. Browser
  session-restore features can restore session cookies, so the idle check remains
  necessary.
- The production session cookie defaults to Secure when `APP_ENV=production`;
  explicit deployment configuration and live HTTPS behavior still need review.
- Existing baseline browser headers were already present in `SecurityHeaders`.

## Second implementation increment - 2026-11-25

- Removed `is_admin` from `User::$fillable`, so generic create/update requests
  cannot set or change administrator privileges through mass assignment.
- Kept the opt-in local/testing bootstrap admin creation explicit with
  `User::forceCreate`; public registration still creates ordinary users only.
- Updated existing admin test fixtures to use explicit trusted fixture creation.
  Tests were not run, in keeping with the project instruction.
- No migration or seeder was run, and no live account was changed.

## Third implementation increment - 2026-11-25

- Composer audit initially reported 12 advisories across seven packages. Applied
  compatible updates within the existing Laravel 12 and Symfony 7.4 lines; the
  targeted update resolved those advisories with 49 transitive patch/minor
  updates, one added polyfill and no removals or major-version upgrades.
- npm audit initially reported five vulnerabilities in the lockfile, including
  critical `shell-quote` advisories. Applied `npm audit fix --package-lock-only`;
  vulnerable packages now resolve to Vite 7.3.8, concurrently 9.2.5,
  follow-redirects 1.16.3, shell-quote 1.12.0 and source-map-js 1.2.4.
- Re-ran both audits successfully: Composer found no advisories and npm found
  zero vulnerabilities. The npm lockfile is updated; local `node_modules` was
  not reinstalled.
- Automated tests and production deployment checks were not run.

## Fourth implementation increment - 2026-11-25

- Added `Content-Security-Policy-Report-Only` with same-origin defaults, no
  plugins/objects, same-origin framing/forms, and the currently observed Google
  Fonts hosts. Local Vite development origins are allowed only in local mode.
- Inline scripts/styles and images outside the local/data/blob sources are
  intentionally reportable for browser review; the header does not block them.
- No reporting collector or enforcing CSP was enabled. Review browser console
  reports on the main store, product, checkout, and admin pages before tightening.
- HSTS and live document-root/session settings remain cPanel verification items.

## Fifth implementation increment - 2026-11-25

- Added mandatory authenticator-app TOTP MFA for the central admin, one-time
  recovery codes, attempt throttling, and a one-time enrollment screen. The
  shared TOTP secret and hashed recovery codes are encrypted at rest through
  Laravel encrypted casts. No MFA vendor or external QR service receives the
  secret.
- Added schema migrations for the admin MFA fields and TOTP replay prevention.
  Both were applied locally after full database backups; neither was run on
  cPanel.
- Added signed email verification for new accounts and email changes, and
  password reset links only for verified email addresses. Responses do not
  disclose whether an email belongs to an account. The existing
  `password_reset_tokens` table is reused. The current `.env.example` mailer is
  `log`; configure and verify SMTP before relying on email delivery.
- Added password-session validation on web requests so password changes end
  other authenticated sessions, plus a ten-minute password confirmation window
  before permanent order deletion, COD payment marking, refund actions, product
  deletion and coupon deletion.
- Admin access now requires verified email and MFA. Existing admin accounts
  must verify the configured email and enroll an authenticator after the
  migration is applied. Customers retain remember-me behavior.
- A TOTP time step can be accepted only once; one-time recovery codes are also
  consumed atomically, preventing replay of a recently accepted code.
- PHP syntax lint and `git diff --check` passed. Automated tests were not run.
- Full local `earthquick_db` dumps were created outside the repository and
  verified before each migration (27 tables, 11 data statements); migrations
  are recorded locally as batches 27 and 28. No seeder or cPanel migration was
  run.

Still pending: configure/test production mail; review CSP browser reports and
staged tightening; inspect
production HTTPS/HSTS, response headers, document root and environment on cPanel;
then complete release checks. Browser and live hosting checks require a running
app / cPanel access. Do not enable enforcing CSP or HSTS until those checks pass.

### MFA migration review / backup checklist

- Scope: add nullable `users` columns for the encrypted TOTP secret, encrypted
  hashed recovery codes, confirmation time and last accepted TOTP step. No
  existing rows are rewritten and no customer/order data is modified.
- Compatibility: app code and both migrations must be deployed together; until
  they are applied, MFA enrollment cannot work. Existing admin access will
  redirect to enrollment after deployment.
- Backup: create a full database dump immediately before migration, confirm it
  is non-empty/readable and retain it outside the public web root. Do not include
  it in Git or place it under `public/`.
- Apply: both migrations are applied locally. Run the Laravel migration command
  on production only after confirming its database backup and target.
- Rollback: first restore compatible application code. Rolling back these
  migrations deletes MFA secrets, recovery codes and replay state, so admins
  must set up MFA again after any rollback/re-apply.

## Boundaries and dependencies

- Application code has changed locally in the increments above. No live
  environment or account settings have been changed.
- Live cPanel document-root, HTTPS and environment values require deployment
  access and owner authorization.
- Any future MFA schema changes need a verified database backup and reviewed
  plan before application; preserve rollback ability.
- Security verification should use a staging copy. Do not run tests or scans that
  modify business data against production.
