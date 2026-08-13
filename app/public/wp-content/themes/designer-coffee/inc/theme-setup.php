<?php
/**
 * Theme module.
 *
 * @package DesignerCoffee
 */

if (!defined('ABSPATH')) {
    exit;
}


if (!defined('DESIGNER_COFFEE_VERSION')) {
    define('DESIGNER_COFFEE_VERSION', '1.0.0');
}

/**
 * Return a cache-busting version for a theme asset.
 *
 * Local development uses the file modification time; production falls back
 * to the theme version if the asset cannot be resolved.
 */
function designer_coffee_asset_version($relative_path) {
    $file_path = get_template_directory() . '/' . ltrim($relative_path, '/');

    return file_exists($file_path) ? (string) filemtime($file_path) : DESIGNER_COFFEE_VERSION;
}


/* ==========================================================================
   1. THEME SETUP
   ========================================================================== */
function designer_coffee_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    register_nav_menus(array(
        'primary' => __('Primary Navigation Menu', 'designer-coffee'),
        'footer'  => __('Footer Navigation Menu', 'designer-coffee'),
    ));
}
add_action('after_setup_theme', 'designer_coffee_setup');

