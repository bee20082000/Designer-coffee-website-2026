<?php
/**
 * Theme module.
 *
 * @package DesignerCoffee
 */

if (!defined('ABSPATH')) {
    exit;
}

/* ==========================================================================
   12. CUSTOM PRODUCT REWRITE SLUG (/shop/ INSTEAD OF /product/) & DOUBLE SLASH FIX
   ========================================================================== */

function designer_coffee_custom_product_slug($args, $post_type) {
    if ($post_type === 'product') {
        $args['rewrite'] = array(
            'slug'       => 'shop',
            'with_front' => false,
            'feeds'      => true,
        );
    }
    return $args;
}
add_filter('register_post_type_args', 'designer_coffee_custom_product_slug', 10, 2);

// Filter post_type_link to guarantee clean /shop/ product permalinks without double slashes
function designer_coffee_fix_product_permalink($post_link, $post) {
    if (is_object($post) && $post->post_type === 'product') {
        // Replace /product/ or double slashes with /shop/
        $post_link = preg_replace('#/(product|products)/#', '/shop/', $post_link);
        $post_link = preg_replace('#([^:])//+#', '$1/', $post_link);
    }
    return $post_link;
}
add_filter('post_type_link', 'designer_coffee_fix_product_permalink', 10, 2);

// Automatically set WooCommerce product permalink base to /shop and flush rewrite rules
function designer_coffee_update_product_permalink_base() {
    if (class_exists('WooCommerce')) {
        $options = get_option('woocommerce_permalinks', array());
        if (empty($options['product_base']) || $options['product_base'] !== '/shop') {
            $options['product_base'] = '/shop';
            update_option('woocommerce_permalinks', $options);
            flush_rewrite_rules();
        }
    }
}


