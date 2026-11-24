# Rthquick Phase 11 - Security hardening plan

**Status: In progress.** The first local application controls are implemented;
source checks and deployment verification remain. This mini-phase follows the
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

## Phase 11.3 - Dependency and release review

- Run Composer and npm advisory audits when network access is available; review
  and update affected dependencies without unreviewed major-version upgrades.
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

Still pending: CSP report-only inventory and configuration, HSTS/production
headers and document-root review on cPanel, `is_admin` mass-assignment hardening,
MFA and verified-email recovery design, dependency advisories, and release checks.
MFA/recovery schema or account changes need a reviewed plan and backup before any
migration is run. No database migration or seeder was run for this increment.

## Boundaries and dependencies

- Application code has changed locally in the first increment above. No live
  environment or account settings have been changed.
- Live cPanel document-root, HTTPS and environment values require deployment
  access and owner authorization.
- If MFA requires a schema migration, prepare a reviewed migration and backup
  plan first; do not run it as part of planning.
- Security verification should use a staging copy. Do not run tests or scans that
  modify business data against production.
