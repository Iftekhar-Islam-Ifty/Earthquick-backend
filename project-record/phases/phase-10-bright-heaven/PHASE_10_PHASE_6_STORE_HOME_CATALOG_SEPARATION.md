# Bright / Nous Telos Phase 6 - Store home and catalog separation

**Status: Implemented locally; browser review pending.** Started 2026-11-19
after the owner approved the marketplace/store journey plan.

## Intended customer journey

```text
Rthquick homepage
  -> choose Nous Telos -> Nous Telos editorial store homepage
       -> view all / choose category -> Nous Telos product catalog and filters
  -> choose Bright -> Bright electrical store homepage
       -> choose electrical category / shop all -> Bright product catalog and filters
```

The existing Nous Telos editorial homepage remains intact as the Nous Telos store
home. The marketplace root is now a neutral entry page with the two store choices.
Bright has a dedicated electrical landing page. Both stores retain a distinct
catalog page for all products, category browsing, search, sorting and filters.
Shared cart, checkout and customer support remain under Rthquick.

## Delivered

- Root homepage presents Nous Telos and Bright as the two stores without a mixed
  product feed.
- `/stores/nous-telos` presents the existing Nous Telos editorial experience;
  its product and category links lead to `/stores/nous-telos/catalog`.
- `/stores/bright` presents electrical category entry points and identifies
  Heaven as Bright's product brand. It does not claim unverified warranty terms.
- `/stores/{slug}/catalog` provides each store's separate existing catalog with
  category, search, sort and filter query handling.
- Legacy catalog filter URLs on `/stores/{slug}` redirect to the matching catalog
  while preserving query parameters.
- Shop navigation category links are store-scoped; existing directory/admin and
  product-detail links continue to open the relevant store homepage.
- No database schema, business records, migration or seeder changes were made.

## Remaining checks / inputs

- Browser review on desktop and mobile is pending.
- Bright's real commercial product and warranty data remains governed by Phase 5
  and must be confirmed by Bright before launch.
