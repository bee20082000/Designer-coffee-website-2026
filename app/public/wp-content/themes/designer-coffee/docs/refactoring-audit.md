# Refactoring audit

Audit date: 2026-08-13

## Architecture

The theme is a classic WordPress theme with WooCommerce support and optional ACF field registration. Root WordPress templates route to feature templates in `pages/`. Shared PHP behavior is loaded from focused `inc/` modules. CSS and JavaScript selectors are treated as external contracts because templates and browser behavior depend on them.

## Completed changes

### Safe

- Reduced `functions.php` to an ordered module loader.
- Split setup, assets, ACF, admin, cart, catalog, checkout, currency, and permalink behavior by responsibility.
- Preserved all existing named PHP functions, WordPress hooks, AJAX action names, hook priorities, template paths, and response keys.
- Consolidated repeated cart fragment/response construction.
- Centralized checkout asset registration in the checkout module.
- Standardized cache-busting versions for local theme assets.

### Low risk / security

- Added a WordPress nonce to internal cart mutation requests.
- Added nonce verification to add, remove, and quantity-update AJAX handlers.
- Applied `wp_unslash()` before sanitizing request strings.

## Review required

### Possibly unused

File: `js/main.js`

Reason it appears unused: no theme PHP file enqueues it and no tracked file references it.

Why usage cannot be ruled out: WordPress content, a plugin, or externally injected markup may load the file directly.

Recommendation: inspect the rendered page source and browser network panel across key pages before deleting it.

### Missing template target

File: `woocommerce.php`

Reference: `pages/cart/template-cart.php`

Issue: the referenced cart template does not exist. The current cart-to-checkout redirect may make this branch unreachable in normal use.

Recommendation: decide whether the project intentionally has no cart page. Do not invent or remove this route until that product decision is confirmed.

### Unregistered permalink updater

Function: `designer_coffee_update_product_permalink_base()`

Reason it appears unused: it is defined but has no registered hook or tracked caller.

Why removal is unsafe: it may be called manually or by external code, and permalink changes can affect live URLs.

Recommendation: leave it available until the permalink lifecycle is explicitly reviewed.

### Visual-only contact form

File: `pages/contact/template-contact.php`

Issue: fields have no names and the form has no tracked submission integration.

Recommendation: treat implementation as a separate feature request because adding submission behavior changes functionality and introduces privacy/spam requirements.

### Large frontend files

Files: `js/global.js`, `pages/home/home.css`, `pages/single-product/single-product.css`

Issue: each contains multiple responsibilities or more than 750 lines.

Recommendation: split only after browser regression testing is available. CSS cascade order and DOM event propagation make a blind mechanical split riskier than the PHP module split.

## Preserved contracts

- AJAX actions: `dc_add_to_cart`, `dc_remove_cart_item`, `dc_remove_from_cart`, `dc_update_cart_qty`.
- Localized browser object: `dc_ajax`.
- Product URL base: `/shop/`.
- ACF keys and field names.
- WooCommerce template override paths.
- Existing CSS classes, element IDs, and `data-*` attributes.
- Existing cart JSON response fields: `fragments`, `cart_hash`, and `count`.

## Validation

- Run `php -l` against every PHP file after PHP changes.
- Run `node --check` against active JavaScript files after JavaScript changes.
- Run `git diff --check` before committing.
- Smoke-test home, shop/archive, single product, checkout, order received, blog, about, contact, and 404 pages in WordPress.
- Test variable-product add to cart, mini-cart quantity changes, removal, and checkout rendering.
