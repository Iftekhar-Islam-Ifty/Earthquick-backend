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

## Additional local QA (2026-09-26)

- Rechecked the homepage, Women catalog, store directory, search results, empty cart, empty checkout and login in Chromium at 1440px, 390px and 320px. Screenshots are kept only in the ignored `storage/qa/phase6-public/` directory.
- Confirmed no document-level horizontal overflow or browser console errors on those pages. Guest access to `/admin` redirects to login.
- Search and cart overlays open and close with Escape on the homepage, catalog, search results and checkout pages, without changing cart contents or placing an order.
- In isolated browser sessions, adding one in-stock product to the cart exposed the populated cart and checkout at 390px and 320px without horizontal overflow or console errors. No checkout form was submitted.
- Fixed a confirmed search-results mobile issue: the `view-3col` class overrode the two-column responsive rule, making cards unreadably narrow; the inline search input also forced its submit button beyond the 320px viewport. Chromium recheck confirms two columns and an in-bounds button at 320px and 390px, with the desktop grid unchanged.
- `composer test` passes: 102 PHP tests / 582 assertions and 10 frontend tests.

The browser checks above used guest sessions only. Authenticated admin screens, payment submission, production-only external services and real hosting configuration have **not** been browser-verified by this pass. No order was submitted.

### Launch blocker found during checkout review

The checkout UI currently labels the `bkash` option as "bKash / Nagad / Rocket" and reveals a hard-coded merchant number (`resources/views/checkout.blade.php`). The backend also accepts a `card` payment value (`CheckoutController`). These do not match the approved COD-and-bKash-only launch rules, and there is no verified bKash payment flow. Do not treat the payment step as production-ready or use the displayed number for live payments; the payment phase must resolve this before launch.

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
