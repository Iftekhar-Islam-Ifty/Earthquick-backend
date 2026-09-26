# Phase 6 — Production Readiness and Quality Assurance

Status: In progress

## Completed in this phase

- Added request-level security headers to every web response: `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, and a restrictive `Permissions-Policy` for camera, microphone, and geolocation.
- Added per-identifier login throttling and per-IP registration throttling. Successful authentication/registration clears its associated limiter.
- Added automated coverage for the security headers and failed-login throttle.
- Protected hidden files in the Apache public web root and added automated coverage for Laravel's `/up` health endpoint.
- Updated the cPanel deployment guide to remove unsafe live-seeding and temporary-route instructions.
- Confirmed the existing PHP and JavaScript suites run locally.

## Browser visual QA status

Completed locally on 2026-09-26 with Playwright Chromium at 1440x1000, 1280x900, 1024x768, 768x1024, 390x844, 360x800 and 320x720. Full-page screenshots and the machine-readable report are stored in the ignored temporary directory `storage/qa/homepage-visual/`.

- No horizontal page overflow or duplicate DOM IDs were detected at any tested viewport.
- Shop menu, hero arrows/dots, New Arrivals, Three Piece, Styled by You controls/filters, search, cart drawer, mobile navigation, and supported Escape-key closing behaviours passed without submitting an order or adding an item to the cart.
- Product image files were confirmed to be served locally. Deferred hover/lazy images can remain unloaded in an automated screenshot even when the visible primary image is available, so they are not reported as broken visual assets.
- The QA browser could not reach Google Fonts because its network policy blocks `fonts.googleapis.com`; fallback fonts rendered. This is an environment-limited external-font request, not a confirmed production failure. Self-hosted font assets remain a possible future reliability improvement.

Playwright is now a development-only dependency and local Chromium is installed for repeatable QA. The production asset build also completes successfully with `npm.cmd run build`.

## Required production handoff checks

These depend on the real hosting account and must be completed at deployment time:

- Use a production `.env` with `APP_ENV=production`, `APP_DEBUG=false`, a real HTTPS `APP_URL`, and secure database/mail credentials.
- Set `SESSION_SECURE_COOKIE=true` after HTTPS is active. HSTS should be configured at the web server/CDN only after confirming the production domain is HTTPS-only.
- Configure log retention/monitoring, database backups, and storage backup/restore verification.
- Run migrations deliberately against the production database; do not use development seeders to modify live catalog ownership.
- Perform browser QA at desktop and mobile viewports, including cart, checkout, search, admin, keyboard navigation, and error-console/network checks.
- Run `composer test`, build production assets as required by the host, then cache config/routes/views after production environment values are set.

## Explicitly out of scope

Real bKash verification, courier integration, notifications, vendor dashboards, commissions, and payouts belong to later approved phases.
