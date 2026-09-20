# Phase 0 — Earthquick Marketplace Business Rules

Status: Approved for implementation planning

Phase 0 freezes the operating rules that later catalog, cart, checkout, order,
payment and admin work must follow. This phase intentionally makes no production
database or storefront behavior change.

## 1. Platform ownership and administration

Earthquick has one central platform administration. The Earthquick admin owns
and manages every vendor/brand, product, order, customer issue, promotion and
catalog decision.

There will be no separate vendor admin, seller dashboard, vendor login or
vendor management portal in the current scope.

Vendor records are storefront identities managed by the central admin, not
independent operators with their own application access.

## 2. Cart and order composition

Customers may add products from multiple vendors to one cart and submit one
combined order.

The order stores vendor ownership at item level so the central admin can see
which vendor each item belongs to. The customer still receives one order number
and one consolidated customer order experience.

This keeps the marketplace convenient for customers while preserving vendor
traceability for admin operations.

## 3. Delivery model

Delivery is calculated and managed order-wise, not vendor-wise.

An order has one delivery address, one delivery charge calculation and one
customer-facing delivery status. The central admin coordinates fulfillment for
all items in the order, including items originating from different vendors.

The order must retain a snapshot of the delivery address, delivery charge and
delivery rule used at checkout.

## 4. Return, cancellation and refund ownership

Earthquick central admin handles customer cancellation, return and refund
requests.

Vendors do not receive a separate support or approval workflow. The admin may
coordinate with a vendor internally, but the customer-facing decision and order
state are controlled by Earthquick.

The later order system should support, at minimum:

- customer cancellation before fulfillment;
- admin-approved return request;
- refund tracking for COD and bKash orders;
- partial item-level refund while retaining the parent order;
- admin notes and a status history for every decision.

## 5. Payment methods for the initial launch

The initial launch supports:

- Cash on Delivery (COD);
- bKash.

Card and other payment gateways are out of the initial scope. bKash must not be
treated as a simple text selection: the later payment phase must define payment
reference, verification status, failed payment handling and refund status.

## 6. Vendor suspension behavior

When a vendor is suspended or made inactive:

- new products from that vendor cannot be purchased;
- the vendor store and its public products are hidden from new discovery;
- products already present in a customer cart are rechecked and rejected at the
  next cart/checkout validation;
- already confirmed orders are not silently deleted;
- the central admin decides whether each affected order continues, is cancelled,
  or is refunded;
- historical orders retain their original vendor and product snapshots.

## 7. Commission and payout policy

There is no commission, seller balance, payout, settlement ledger or vendor
financial dashboard in the current scope.

Vendor ownership is used for catalog organization, product attribution and
admin fulfillment visibility only.

## 8. Consequences for later phases

The next implementation phases must preserve these rules:

1. Checkout must revalidate stock, current price and vendor activity.
2. Order items must retain vendor ownership and immutable product snapshots.
3. Delivery and customer-facing tracking remain order-level.
4. Admin remains the only operational control surface.
5. Return/refund data must support both whole-order and item-level actions.
6. Payment implementation must support COD and verified bKash states.

## Approved decisions

| Topic | Decision |
|---|---|
| Administration | One central Earthquick admin |
| Vendor dashboards | Not required |
| Multi-vendor cart | Allowed |
| Delivery | Order-wise |
| Returns/refunds | Central admin-owned |
| Initial payment | COD and bKash |
| Vendor suspension | Stop new purchases; preserve and review existing orders |
| Commission/payout | Not in scope |
