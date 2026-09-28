# Phase 7 — COD and bKash Online Payment Gateway

Status: COD-only local implementation complete; bKash integration explicitly deferred by the owner until the full site and testing are complete and merchant onboarding has been discussed. bKash online payments are **not enabled** and no live-payment claim is made. Production deployment and handoff remain separate.

## Approved payment scope

- Earthquick accepts one combined order across brands, administered centrally.
- Initial methods are COD and bKash online gateway only. Card, Nagad, Rocket, manual transfers, commissions and vendor payouts are not part of this phase.
- A browser redirect or customer-provided transaction ID must never by itself mark an order paid. Payment success requires server-side verification against bKash.

## First safety slice implemented

- Added explicit order payment fields: `payment_status`, unique nullable `payment_reference`, and `paid_at`. Historical orders default to `unknown`; their payment history is not guessed from the payment method.
- New COD orders record `due_on_delivery` and remain otherwise compatible with the current checkout flow.
- Server-side checkout rejects `card` and rejects `bkash` before creating an order, using a fail-closed response. The bKash choice is disabled in the checkout UI until the actual gateway is ready.
- Removed the placeholder merchant number and the unsupported Nagad/Rocket wording. No manual payment should be sent based on this site.
- Added feature tests for COD state and for the guarantee that an attempted bKash/card submission cannot create an order, decrement stock, or clear the cart.
- Checked the disabled bKash option visually in isolated guest browser sessions at 390px and 320px. Screenshots are ignored under `storage/qa/phase7/`; no order was submitted.

## COD collection and payment consistency completed

- Delivery and payment are separate states. Changing an order to `delivered` does **not** mark it paid.
- On a delivered, unpaid COD order, the central admin can confirm actual receipt of the full amount and select either in-house cash collection or courier remittance. Courier delivery alone is not proof of remittance. The action records `paid_at`, the admin user, collection channel, and optional receipt/note; it cannot be submitted twice or used for a non-COD/cancelled/undelivered order.
- A paid COD order cannot be moved out of delivered or cancelled through the ordinary status form without a separately designed refund/correction workflow.
- Admin order detail/list, customer order views, invoice, and exported CSV distinguish due, paid and unverified historical payments. Historical records are never silently converted to paid. The About FAQ no longer advertises inactive mobile banking or cards.
- Isolated tests cover authorization, explicit confirmation, non-delivered and non-COD rejection, duplicate submissions, the separate delivery transition, and stale payment claims. No production order, payment or migration was run.
- The administrator must reconcile actual cash or courier settlement before using “Mark COD Paid”. The button is a manual accounting record, not a payment gateway or independent proof of funds.

## Local database migration check (2026-09-27)

- The current Laravel environment is `local` with MySQL at `127.0.0.1`, database `earthquick_db`; this is not production hosting.
- Before migration, a complete 18-table SQL dump was saved in the ignored private QA directory `storage/qa/phase7-db-backup/earthquick_db_pre_phase7_2026-09-27.sql` (SHA-256 `12DBA6145A448124FFEE0459E847AB7B6E505817D0E02D49CC258C99405CA751`). This backup contains database data and must never be committed or shared publicly.
- Both order migrations (`2026_09_27_090000_add_payment_status_to_orders_table.php` and `2026_09_27_100000_add_cod_collection_audit_to_orders_table.php`) ran successfully on this local database and show as Ran in `migrate:status`. All five existing orders retained `payment_status=unknown`; none was inferred paid.
- The local homepage responded HTTP 200. `composer test` passed 112 PHP tests / 642 assertions and 10 frontend tests; `npm.cmd run build` passed.
- **Production sign-off is not complete.** No deployed database migration, restore drill, real-host HTTPS/secrets/backup verification, or deployed browser QA was performed. Do not infer that a production database has these columns until migration status is checked there. Review and back up production data before applying either migration.

## Deferred bKash slice — required before enabling bKash

1. Obtain the approved merchant integration contract, sandbox credentials and exact API/base URL details from bKash. Keep secrets out of the repository. Use the official sandbox for integration tests; production credentials are separate.
2. Implement server-side gateway initiation and a payment-attempt record tied to one order/amount/currency. Never expose API secrets to browser JavaScript.
3. Introduce a bounded stock reservation for pending bKash orders. Define expiry, cancellation and restoration of product/variant stock and coupon usage so abandoned or failed payments do not consume inventory indefinitely.
4. Implement idempotent callback handling and server-side execute/query verification. Match payment ID, order reference, BDT amount and successful transaction state before setting `paid_at` or showing a paid receipt. Handle duplicate callbacks, timeout, failure and cancellation safely.
5. Show payment state and verified transaction reference to the customer and central admin. Add reconciliation and refund handling according to the later approved after-sales rules.
6. Run automated fake-response tests, official bKash sandbox success/failure tests, and mobile/desktop browser QA. Only then enable the bKash option for real users.

Official starting points: [bKash product overview](https://developer.bka.sh/docs/product-overview), [Checkout overview](https://developer.bka.sh/docs/checkout-overview), and [bKash merchant sandbox](https://merchantdemo.sandbox.bka.sh/).

Do not apply the new migration to a live database without a reviewed backup and deployment plan.
