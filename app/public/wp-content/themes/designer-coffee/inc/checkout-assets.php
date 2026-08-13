<?php
/**
 * Checkout-specific asset enqueues (checkout + thankyou)
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('designer_coffee_enqueue_checkout_assets')) {
    function designer_coffee_enqueue_checkout_assets() {
        // Broaden checks so styles load reliably when using custom templates
        $should_load_checkout = false;
        $should_load_thankyou = false;

        if (function_exists('is_checkout') && is_checkout()) {
            $should_load_checkout = true;
        }

        if (function_exists('is_order_received_page') && (is_order_received_page() || (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-received')))) {
            $should_load_thankyou = true;
        }

        // Also load when using our page templates or the explicit "checkout" page slug
        if (function_exists('is_page_template') && is_page_template('pages/checkout/template-checkout.php')) {
            $should_load_checkout = true;
        }
        if (function_exists('is_page_template') && is_page_template('pages/checkout/template-thankyou.php')) {
            $should_load_thankyou = true;
        }
        if (function_exists('is_page') && is_page('checkout')) {
            $should_load_checkout = true;
        }

        if ($should_load_checkout) {
            wp_enqueue_style(
                'designer-coffee-checkout-css',
                get_template_directory_uri() . '/pages/checkout/checkout.css',
                array('dc-global'),
                designer_coffee_asset_version('pages/checkout/checkout.css')
            );
            // Compact review-order styles
            wp_enqueue_style(
                'designer-coffee-review-order-css',
                get_template_directory_uri() . '/pages/checkout/review-order.css',
                array('designer-coffee-checkout-css'),
                designer_coffee_asset_version('pages/checkout/review-order.css')
            );
        }

        if ($should_load_thankyou) {
            wp_enqueue_style(
                'designer-coffee-thankyou-css',
                get_template_directory_uri() . '/pages/checkout/thankyou.css',
                array('dc-global'),
                designer_coffee_asset_version('pages/checkout/thankyou.css')
            );
        }
    }
    // Enqueue earlier to improve reliability when other plugins/themes alter load order
    add_action('wp_enqueue_scripts', 'designer_coffee_enqueue_checkout_assets', 5);
}
