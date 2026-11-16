# Phase 4 - checkout and order correctness

Completed 2026-09-18. This phase hardens the existing central checkout flow; it
does not add payments, split fulfilment, vendor portals, commissions, or payouts.

## Changes

- Cart add and quantity updates now reject unavailable products and quantities
  above recorded stock.
- Checkout reloads every product inside its transaction, locks inventory rows,
  uses current product prices and vendor status, and rejects unavailable items.
- Successful checkout decrements stock and marks an item out of stock when its
  quantity reaches zero.
- The BDT 3,000 free-delivery promise now applies consistently to cart, checkout
  presentation, and the persisted order total.
- Guest confirmation pages are restricted to the session that placed the order.
  Authenticated account pages require an exact `orders.user_id` match; matching
  phone numbers or email addresses no longer grant access.

## Verification

`composer test` passed: **81 PHP tests, 399 assertions, and 10 frontend
interaction tests**.

`composer test:regressions` passed: **8 checkout/order correctness tests and
14 assertions**. The targeted suite remains available for quick checkout-flow
verification and is now also included in the normal PHP test run.
