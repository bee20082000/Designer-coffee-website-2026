<?php
/**
 * Single Product Template
 * Location: single-product.php (Theme Root)
 * Standard WooCommerce single product entry point.
 * Delegates rendering to pages/single-product/template-single-product.php
 *
 * @package DesignerCoffee
 */

if (!defined('ABSPATH')) {
    exit;
}

require get_template_directory() . '/pages/single-product/template-single-product.php';
