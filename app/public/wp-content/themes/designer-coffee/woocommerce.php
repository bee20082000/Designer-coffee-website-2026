<?php
/**
 * WooCommerce Template Override
 * This file intercepts ALL WooCommerce page requests (shop, categories, product archives).
 * Routes them through our custom shop template and product card component.
 *
 * @package DesignerCoffee
 */

if (!defined('ABSPATH')) {
    exit;
}

if (is_singular('product') || (function_exists('is_product') && is_product())) {
    require get_template_directory() . '/pages/single-product/template-single-product.php';
} elseif (function_exists('is_cart') && is_cart()) {
    require get_template_directory() . '/pages/cart/template-cart.php';
} elseif (function_exists('is_checkout') && is_checkout()) {
    require get_template_directory() . '/pages/checkout/template-checkout.php';
} else {
    require get_template_directory() . '/pages/shop/template-shop.php';
}


