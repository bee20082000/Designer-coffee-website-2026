# Designer Coffee theme

This is a custom classic WordPress theme with WooCommerce and optional ACF support.

## Folder responsibilities

- `functions.php`: theme bootstrap, hooks, and shared PHP behavior. Keep page markup out of this file.
- `inc/`: focused PHP modules that register hooks or provide shared services.
- `template-parts/`: reusable view fragments with little or no business logic.
- `pages/<feature>/`: a page template and its feature-specific styles.
- `css/components/`: global design-system tokens and reusable UI components.
- `js/`: global and feature-specific browser behavior.
- `woocommerce/`: WooCommerce template overrides only.
- `images/` and `fonts/`: theme-owned static assets.

## Maintenance rules

1. Put reusable PHP behavior in a named `inc/*.php` module and load it once from `functions.php`.
2. Enqueue assets from PHP hooks; do not enqueue styles from page templates.
3. Use `designer_coffee_asset_version()` for local assets so browser caches update automatically.
4. Prefix global PHP functions, AJAX actions, script handles, and CSS components with `designer_coffee_` or `dc-`.
5. Escape output at render time and sanitize request data before use.
6. Keep page-specific CSS/JS beside its feature; promote styles to `css/components/` only when reused.
7. Check every PHP change with `php -l` before deployment.

## Recommended next splits

`functions.php` should continue moving toward a small loader. The next safe modules are:

- `inc/theme-setup.php`: theme support, menus, and admin cleanup.
- `inc/assets.php`: all style/script registration.
- `inc/acf-fields.php`: local ACF field groups.
- `inc/woocommerce/cart.php`: mini-cart rendering and AJAX actions.
- `inc/woocommerce/catalog.php`: product cards, filters, and permalinks.
- `inc/woocommerce/checkout.php`: checkout presentation hooks and translations.

Move one responsibility at a time and smoke-test the affected page after each move.
