# Admin order retention

Earthquick Admin → Customer Orders has three folders: Active, Archive and Trash.

- Archive is available for delivered or cancelled orders. It hides them from the active list, but keeps all data, financial reports and the customer-facing order record. Archive has no expiry; admin can restore an order to Active at any time.
- Trash is available only for cancelled, unpaid, unfulfilled COD orders with a completed cancellation and no payment, dispatch, return or refund history. An order may be moved from Active or Archive. It disappears from normal order queries and the customer's order history while in Trash, but its data and related records remain in the database.
- Trash restore is available at any time. A previously archived order returns to Archive; otherwise it returns to Active. Restoring does not change stock, payment or coupon usage.
- Permanent deletion is locked for 30 days after moving to Trash. After that, an admin may explicitly purge with an exact order-number confirmation. No scheduled or automatic purge exists. Purge removes the order and its dependent records, leaves a minimal audit, and does not change stock again. Paid or fulfilled orders cannot be purged by this flow.

Deployment: back up the production database, deploy the code, then run `php artisan migrate --force` in the Laravel project directory. The migration only adds nullable order-retention columns; it does not archive, trash or delete existing orders. If views/routes were cached before deployment, rebuild or clear those caches after deployment.
