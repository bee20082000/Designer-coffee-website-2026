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
   2. ENQUEUE STYLES & SCRIPTS
   ========================================================================== */
function designer_coffee_scripts() {

    // Global CSS — all pages
    wp_enqueue_style('dc-global', get_template_directory_uri() . '/css/global.css', array(), designer_coffee_asset_version('css/global.css'));

    // Home CSS — front page only
    if (is_front_page() || is_page_template('pages/home/template-home.php')) {
        wp_enqueue_style('dc-home', get_template_directory_uri() . '/pages/home/home.css', array('dc-global'), designer_coffee_asset_version('pages/home/home.css'));
    }

    // Shop CSS — WooCommerce shop, product archives, categories, AND page-template fallback
    // NOTE: This is a separate if (not elseif) so it always loads on WooCommerce pages
    $is_any_shop_page = (
        (function_exists('is_shop') && is_shop()) ||
        is_post_type_archive('product') ||
        (function_exists('is_product_taxonomy') && is_product_taxonomy()) ||
        (function_exists('is_product_category') && is_product_category()) ||
        (function_exists('is_product_tag') && is_product_tag()) ||
        is_page('shop') ||
        is_page_template('pages/shop/template-shop.php')
    );
    if ($is_any_shop_page) {
        wp_enqueue_style('dc-shop', get_template_directory_uri() . '/pages/shop/shop.css', array('dc-global'), designer_coffee_asset_version('pages/shop/shop.css'));
    }

    // Other page-specific CSS
    if (!is_front_page() && !is_page_template('pages/home/template-home.php') && !$is_any_shop_page) {
        if (is_page('about') || is_page_template('pages/about/template-about.php')) {
            wp_enqueue_style('dc-about', get_template_directory_uri() . '/pages/about/about.css', array('dc-global'), designer_coffee_asset_version('pages/about/about.css'));
        } elseif (is_page('contact') || is_page_template('pages/contact/template-contact.php')) {
            wp_enqueue_style('dc-contact', get_template_directory_uri() . '/pages/contact/contact.css', array('dc-global'), designer_coffee_asset_version('pages/contact/contact.css'));
        } elseif (is_home() || is_single() || is_page('blog') || is_page_template('pages/blog/template-blog.php')) {
            wp_enqueue_style('dc-blog', get_template_directory_uri() . '/pages/blog/blog.css', array('dc-global'), designer_coffee_asset_version('pages/blog/blog.css'));
        }
    }

    // GSAP animation engine (CDN, all pages)
    wp_enqueue_script('gsap',               'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',           array(),       '3.12.5', true);
    wp_enqueue_script('gsap-text',          'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/TextPlugin.min.js',      array('gsap'), '3.12.5', true);
    wp_enqueue_script('gsap-scrolltrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',  array('gsap'), '3.12.5', true);

    // Global JS — sticky nav, dropdown, back-to-top (all pages)
    wp_enqueue_script('dc-global-js', get_template_directory_uri() . '/js/global.js', array('gsap'), designer_coffee_asset_version('js/global.js'), true);
    wp_localize_script('dc-global-js', 'dc_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('designer_coffee_cart'),
    ));

    // Home-page-only JS — hero slideshow, letter card, gallery parallax, product carousel
    if (is_front_page() || is_page_template('pages/home/template-home.php')) {
        wp_enqueue_script('dc-home-js', get_template_directory_uri() . '/js/home.js', array('gsap', 'gsap-text', 'gsap-scrolltrigger'), designer_coffee_asset_version('js/home.js'), true);
        wp_enqueue_script('dc-home-carousel', get_template_directory_uri() . '/js/home-carousel.js', array('dc-global-js'), designer_coffee_asset_version('js/home-carousel.js'), true);
        wp_add_inline_script('gsap-text',          'gsap.registerPlugin(TextPlugin);');
        wp_add_inline_script('gsap-scrolltrigger', 'gsap.registerPlugin(ScrollTrigger);');
    }


    // Single Product CSS & JS
    if (is_singular('product') || (function_exists('is_product') && is_product()) || is_page_template('pages/single-product/template-single-product.php')) {
        wp_enqueue_style('dc-single-product', get_template_directory_uri() . '/pages/single-product/single-product.css', array('dc-global'), designer_coffee_asset_version('pages/single-product/single-product.css'));
        wp_enqueue_script('dc-single-product-js', get_template_directory_uri() . '/js/single-product.js', array('dc-global-js'), designer_coffee_asset_version('js/single-product.js'), true);
    }

}
add_action('wp_enqueue_scripts', 'designer_coffee_scripts');
