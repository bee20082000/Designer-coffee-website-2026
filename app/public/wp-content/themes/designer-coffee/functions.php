<?php
/**
 * Designer Coffee — functions.php
 * Modular architecture: global CSS/JS on every page, page-specific CSS/JS only where needed.
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
            wp_enqueue_style('dc-about', get_template_directory_uri() . '/pages/about/about.css', array('dc-global'), '31.0.0');
        } elseif (is_page('contact') || is_page_template('pages/contact/template-contact.php')) {
            wp_enqueue_style('dc-contact', get_template_directory_uri() . '/pages/contact/contact.css', array('dc-global'), '31.0.0');
        } elseif (is_home() || is_single() || is_page('blog') || is_page_template('pages/blog/template-blog.php')) {
            wp_enqueue_style('dc-blog', get_template_directory_uri() . '/pages/blog/blog.css', array('dc-global'), '31.0.0');
        }
    }

    // GSAP animation engine (CDN, all pages)
    wp_enqueue_script('gsap',               'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',           array(),       '3.12.5', true);
    wp_enqueue_script('gsap-text',          'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/TextPlugin.min.js',      array('gsap'), '3.12.5', true);
    wp_enqueue_script('gsap-scrolltrigger', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js',  array('gsap'), '3.12.5', true);

    // Global JS — sticky nav, dropdown, back-to-top (all pages)
    wp_enqueue_script('dc-global-js', get_template_directory_uri() . '/js/global.js', array('gsap'), designer_coffee_asset_version('js/global.js'), true);
    wp_localize_script('dc-global-js', 'dc_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));

    // Home-page-only JS — hero slideshow, letter card, gallery parallax, product carousel
    if (is_front_page() || is_page_template('pages/home/template-home.php')) {
        wp_enqueue_script('dc-home-js', get_template_directory_uri() . '/js/home.js', array('gsap', 'gsap-text', 'gsap-scrolltrigger'), '1.7.0', true);
        wp_enqueue_script('dc-home-carousel', get_template_directory_uri() . '/js/home-carousel.js', array('dc-global-js'), '31.0.0', true);
        wp_add_inline_script('gsap-text',          'gsap.registerPlugin(TextPlugin);');
        wp_add_inline_script('gsap-scrolltrigger', 'gsap.registerPlugin(ScrollTrigger);');
    }


    // Single Product CSS & JS
    if (is_singular('product') || (function_exists('is_product') && is_product()) || is_page_template('pages/single-product/template-single-product.php')) {
        wp_enqueue_style('dc-single-product', get_template_directory_uri() . '/pages/single-product/single-product.css', array('dc-global'), '31.0.0');
        wp_enqueue_script('dc-single-product-js', get_template_directory_uri() . '/js/single-product.js', array('dc-global-js'), '31.0.0', true);
    }

}
add_action('wp_enqueue_scripts', 'designer_coffee_scripts');




/* ==========================================================================
   3. ACF HELPER — get_field() with safe fallback
   ========================================================================== */
function designer_get_field($field_name, $post_id = false, $default = '') {
    if (function_exists('get_field')) {
        $val = get_field($field_name, $post_id);
        if ($val !== null && $val !== false && $val !== '') {
            return $val;
        }
    }
    return $default;
}

/* ==========================================================================
   4. ACF FIELD GROUPS — registered in code, appear in page editor when ACF
      plugin is active. Site works with hardcoded defaults if ACF is absent.
   ========================================================================== */
add_action('acf/init', 'designer_coffee_register_acf_fields');

function designer_coffee_register_acf_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    // --- Homepage hero slides (Front Page) ---
    acf_add_local_field_group(array(
        'key'    => 'group_dc_homepage',
        'title'  => '🏠 Homepage Content',
        'fields' => array(
            array('key' => 'field_dc_hero0_tab',          'label' => 'Hero Slide 1',           'name' => '',                  'type' => 'tab'),
            array('key' => 'field_dc_hero_slide0_title',  'label' => 'Slide 1 — Headline',     'name' => 'hero_slide0_title', 'type' => 'text',     'placeholder' => 'Design for Lover, wildly loved.',       'instructions' => 'Bold headline on the first hero slide.'),
            array('key' => 'field_dc_hero_slide0_desc',   'label' => 'Slide 1 — Description',  'name' => 'hero_slide0_desc',  'type' => 'textarea', 'rows' => 3,  'placeholder' => 'Sustainable coffee products…',          'instructions' => 'Short description below the headline.'),
            array('key' => 'field_dc_hero1_tab',          'label' => 'Hero Slide 2',           'name' => '',                  'type' => 'tab'),
            array('key' => 'field_dc_hero_slide1_title',  'label' => 'Slide 2 — Headline',     'name' => 'hero_slide1_title', 'type' => 'text',     'placeholder' => 'SUSTAINABLE AGRICULTURE'),
            array('key' => 'field_dc_hero_slide1_desc',   'label' => 'Slide 2 — Description',  'name' => 'hero_slide1_desc',  'type' => 'textarea', 'rows' => 3,  'placeholder' => 'We cultivate fair relationships…'),
            array('key' => 'field_dc_hero2_tab',          'label' => 'Hero Slide 3',           'name' => '',                  'type' => 'tab'),
            array('key' => 'field_dc_hero_slide2_title',  'label' => 'Slide 3 — Headline',     'name' => 'hero_slide2_title', 'type' => 'text',     'placeholder' => 'Education, Workshop & Tours'),
            array('key' => 'field_dc_hero_slide2_desc',   'label' => 'Slide 3 — Description',  'name' => 'hero_slide2_desc',  'type' => 'textarea', 'rows' => 3,  'placeholder' => 'Through farm education, immersive tours…'),
        ),
        'location' => array(array(array('param' => 'page_type', 'operator' => '==', 'value' => 'front_page'))),
        'menu_order' => 0, 'position' => 'normal', 'style' => 'default', 'label_placement' => 'top', 'instruction_placement' => 'label',
    ));

    // --- About page ---
    acf_add_local_field_group(array(
        'key'    => 'group_dc_about',
        'title'  => '☕ About Page Content',
        'fields' => array(
            array('key' => 'field_dc_about_heading', 'label' => 'Main Heading', 'name' => 'about_heading', 'type' => 'text',     'instructions' => 'Large h1 on the About page.',         'placeholder' => 'Designer Coffee: Where Coffee is Loved in Its Most Primitive Form'),
            array('key' => 'field_dc_about_body',    'label' => 'Body Text',    'name' => 'about_body',    'type' => 'textarea', 'instructions' => 'Paragraph text below the heading.', 'rows' => 5, 'placeholder' => 'At Designer Coffee, coffee is not just a drink…'),
        ),
        'location' => array(array(array('param' => 'page_template', 'operator' => '==', 'value' => 'pages/about/template-about.php'))),
        'menu_order' => 0, 'position' => 'normal', 'style' => 'default', 'label_placement' => 'top',
    ));
}

/* ==========================================================================
   5. CLEAN ADMIN SIDEBAR — hide menu items irrelevant to this project
      To restore any item, comment out or remove its remove_menu_page() line.
   ========================================================================== */
add_action('admin_menu', 'designer_coffee_clean_admin_menu', 999);

function designer_coffee_clean_admin_menu() {
    remove_menu_page('edit-comments.php');                    // Comments (not used)
}

/* ==========================================================================
   6. CLEAN ADMIN BAR
   ========================================================================== */
add_action('wp_before_admin_bar_render', 'designer_coffee_clean_admin_bar');

function designer_coffee_clean_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_node('wp-logo');     // WordPress logo menu
    $wp_admin_bar->remove_node('comments');    // Comments shortcut
}

/* ==========================================================================
   7. CLEAN DASHBOARD WIDGETS
   ========================================================================== */
add_action('wp_dashboard_setup', 'designer_coffee_clean_dashboard');

function designer_coffee_clean_dashboard() {
    remove_meta_box('dashboard_quick_press',    'dashboard', 'side');
    remove_meta_box('dashboard_primary',        'dashboard', 'side');
    remove_meta_box('dashboard_activity',       'dashboard', 'normal');
    remove_meta_box('wc_admin_dashboard_setup', 'dashboard', 'normal');
}

/* ==========================================================================
   8. WOOCOMMERCE HEADER MINI-CART & AJAX FRAGMENTS
   ========================================================================== */
function designer_coffee_header_cart() {
    $is_woo = class_exists('WooCommerce') && function_exists('WC') && WC()->cart;
    $cart = $is_woo ? WC()->cart : null;
    if ($cart) {
        $cart->calculate_totals();
        $count = $cart->get_cart_contents_count();
        $subtotal = $cart->get_cart_subtotal();
    } else {
        $count = 0;
        $subtotal = function_exists('wc_price') ? wc_price(0) : '$0.00';
    }
    $cart_url = $is_woo ? wc_get_page_permalink('cart') : home_url('/cart');
    $checkout_url = $is_woo ? wc_get_page_permalink('checkout') : home_url('/checkout');
    $shop_url = $is_woo ? wc_get_page_permalink('shop') : home_url('/shop');
    ?>

    <div class="header-cart-wrapper">
        <button type="button" class="nav-cart-btn nav-cart-text" aria-label="Shopping Cart" aria-haspopup="true" aria-expanded="false">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <path d="M16 10a4 4 0 0 1-8 0"></path>
            </svg>
            <span class="cart-count-badge cart-count"><?php echo esc_html($count); ?></span>
        </button>


        <div class="cart-dropdown-panel">
            <div class="cart-dropdown-header">
                <span class="cart-dropdown-title">Cart Review</span>
                <span class="cart-dropdown-count"><?php echo esc_html($count); ?> <?php echo ($count === 1 ? 'item' : 'items'); ?></span>
            </div>

            <div class="cart-dropdown-body">
                <?php if (!$cart || $cart->is_empty()) : ?>
                    <div class="cart-empty-message">
                        <p>Your cart is currently empty.</p>
                        <a href="<?php echo esc_url($shop_url); ?>" class="cart-shop-btn">Explore Shop</a>
                    </div>
                <?php else : ?>
                    <ul class="cart-items-list">
                        <?php
                        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
                            $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                            if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                                $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                                $thumbnail         = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('thumbnail', array('class' => 'cart-item-img-el')), $cart_item, $cart_item_key);
                                $product_name      = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
                                $product_price     = apply_filters('woocommerce_cart_item_price', $cart->get_product_price($_product), $cart_item, $cart_item_key);
                                $remove_url        = wc_get_cart_remove_url($cart_item_key);

                                $parent_product = $_product->is_type('variation') ? wc_get_product($_product->get_parent_id()) : $_product;

                                $base_name = $parent_product ? $parent_product->get_name() : $_product->get_name();

                                // Extract Weight
                                $weight_val = '';
                                if ($_product->is_type('variation')) {
                                    $var_attrs = $_product->get_variation_attributes();
                                    if (!empty($var_attrs)) {
                                        foreach ($var_attrs as $k => $v) {
                                            if (!empty($v)) {
                                                $weight_val = $v;
                                                break;
                                            }
                                        }
                                    }
                                }
                                if (!$weight_val) {
                                    $weight_attr = $_product->get_attribute('weight') ?: $_product->get_attribute('size') ?: $_product->get_attribute('peso');
                                    if ($weight_attr) {
                                        $weight_val = $weight_attr;
                                    }
                                }
                                if (!$weight_val) {
                                    $weight_val = '250g';
                                }

                                // Avoid duplicate weight text if base_name already contains weight
                                if (stripos($base_name, $weight_val) !== false) {
                                    $name_and_weight = $base_name;
                                } else {
                                    $name_and_weight = $base_name . ' - ' . $weight_val;
                                }

                                // Extract Grind Size
                                $grind_text = 'Whole Bean';
                                if (!empty($cart_item['cgs_grind'])) {
                                    $cgs_opts = function_exists('cgs_get_grind_options') ? cgs_get_grind_options() : array();
                                    $g_key = $cart_item['cgs_grind'];
                                    if (isset($cgs_opts[$g_key]['label'])) {
                                        $grind_text = $cgs_opts[$g_key]['label'];
                                    } else {
                                        $grind_text = ucfirst(str_replace('-', ' ', $g_key));
                                    }
                                } else {
                                    $item_data = WC()->cart->get_item_data($cart_item, true);
                                    if ($item_data) {
                                        $lines = explode("\n", trim(strip_tags($item_data)));
                                        if (!empty($lines[0])) {
                                            $grind_text = trim($lines[0]);
                                        }
                                    }
                                }
                                ?>
                                <li class="cart-item">
                                    <div class="cart-item-img">
                                        <?php if (!empty($product_permalink)) : ?>
                                            <a href="<?php echo esc_url($product_permalink); ?>"><?php echo $thumbnail; ?></a>
                                        <?php else : ?>
                                            <?php echo $thumbnail; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="cart-item-details">
                                        <!-- Line 1: Name - Weight -->
                                        <h5 class="cart-item-title">
                                            <?php if (!empty($product_permalink)) : ?>
                                                <a href="<?php echo esc_url($product_permalink); ?>"><?php echo esc_html($name_and_weight); ?></a>
                                            <?php else : ?>
                                                <?php echo esc_html($name_and_weight); ?>
                                            <?php endif; ?>
                                        </h5>

                                        <!-- Line 2: Grind Size -->
                                        <div class="cart-item-grind">
                                            <strong class="designer-grind-value"><?php echo esc_html($grind_text); ?></strong>
                                        </div>

                                        <!-- Line 3: Quantity (- / + capsule) x Price -->
                                        <div class="cart-item-row-3">
                                            <div class="mini-qty-capsule">
                                                <button type="button" class="mini-qty-btn mini-qty-minus" data-cart_item_key="<?php echo esc_attr($cart_item_key); ?>" aria-label="Decrease quantity">−</button>
                                                <span class="mini-qty-val"><?php echo esc_html($cart_item['quantity']); ?></span>
                                                <button type="button" class="mini-qty-btn mini-qty-plus" data-cart_item_key="<?php echo esc_attr($cart_item_key); ?>" aria-label="Increase quantity">+</button>
                                            </div>
                                            <span class="cart-item-multiply">&times;</span>
                                            <span class="cart-item-price"><?php echo $product_price; ?></span>
                                        </div>
                                    </div>
                                    <a href="<?php echo esc_url($remove_url); ?>" class="cart-item-remove" aria-label="Remove item" data-cart_item_key="<?php echo esc_attr($cart_item_key); ?>">&times;</a>
                                </li>

                                <?php
                            }
                        }
                        ?>
                    </ul>
                <?php endif; ?>
            </div>

            <?php if ($cart && !$cart->is_empty()) : ?>
                <div class="cart-dropdown-footer">
                    <div class="cart-subtotal-row">
                        <span>Total:</span>
                        <strong class="cart-subtotal-price"><?php echo $subtotal; ?></strong>
                    </div>
                    <div class="cart-actions-row">
                        <a href="<?php echo esc_url($checkout_url); ?>" class="btn-checkout">PROCEED TO CHECKOUT &rarr;</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/* Redirect Cart Page Directly to Checkout */
add_action('template_redirect', function() {
    if (function_exists('is_cart') && is_cart() && !is_checkout()) {
        wp_safe_redirect(wc_get_checkout_url(), 301);
        exit;
    }
});


function designer_coffee_cart_count_fragment($fragments) {
    ob_start();
    designer_coffee_header_cart();
    $fragments['div.header-cart-wrapper'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'designer_coffee_cart_count_fragment');

/* ==========================================================================
   9. REUSABLE PRODUCT CARD COMPONENT
   ========================================================================== */
function designer_coffee_render_product_card($product_data = null, $args = array()) {
    $product = null;
    $product = null;
    $product_id = 0;
    $title = '';
    $permalink = '#';
    $price_html = '';
    $image_html = '';
    $is_sold_out = false;

    if (is_a($product_data, 'WC_Product')) {
        $product = $product_data;
    } elseif (is_numeric($product_data)) {
        $product = function_exists('wc_get_product') ? wc_get_product($product_data) : null;
    } elseif (empty($product_data)) {
        global $product;
        if (!$product && function_exists('wc_get_product')) {
            $product = wc_get_product(get_the_ID());
        }
    }

    if ($product && is_a($product, 'WC_Product')) {
        $product_id  = $product->get_id();
        $permalink   = get_permalink($product_id);
        $title       = $product->get_name();
        if ($product->is_type('variable')) {
            $min_price = $product->get_variation_price('min', true);
            $price_html = '<span class="price-from">From </span>' . wc_price($min_price);
        } else {
            $price_html = $product->get_price_html();
        }


        $is_sold_out = !$product->is_in_stock();
        if (has_post_thumbnail($product_id)) {
            $image_html = get_the_post_thumbnail($product_id, 'large', array('class' => 'product-img', 'alt' => esc_attr($title)));
        } else {
            $image_html = '<img src="' . esc_url(home_url('/wp-content/uploads/2026/02/250GR_PRODUCT_HONEYQ.png')) . '" alt="' . esc_attr($title) . '" class="product-img">';
        }
    } elseif (is_array($product_data)) {
        $product_id  = isset($product_data['id']) ? intval($product_data['id']) : 0;
        $title       = isset($product_data['title']) ? $product_data['title'] : 'Coffee Product';
        $permalink   = isset($product_data['permalink']) ? $product_data['permalink'] : (class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/shop'));
        $price_html  = isset($product_data['price']) ? $product_data['price'] : '';
        $is_sold_out = !empty($product_data['is_sold_out']);
        $img_src     = isset($product_data['image']) ? $product_data['image'] : home_url('/wp-content/uploads/2026/02/250GR_PRODUCT_HONEYQ.png');
        $image_html  = '<img src="' . esc_url($img_src) . '" alt="' . esc_attr($title) . '" class="product-img">';
    } else {
        return;
    }

    $categories_list = array('all');
    $beans_list      = array('all');
    $proc_list       = array('all');
    $brew_list       = array('all');

    // WooCommerce Attributes extraction (Bean Type, Processing, Brew Method)
    if (!$product && $product_id > 0 && function_exists('wc_get_product')) {
        $product = wc_get_product($product_id);
    }

    if ($product && is_a($product, 'WC_Product')) {
        $wc_attributes = $product->get_attributes();
        if (!empty($wc_attributes)) {
            foreach ($wc_attributes as $attr_key => $attr) {
                $label = strtolower(wc_attribute_label($attr->get_name()));
                $name  = strtolower($attr->get_name());
                $vals  = array();

                if ($attr->is_taxonomy()) {
                    $terms = wc_get_product_terms($product_id, $attr->get_name(), array('fields' => 'all'));
                    if (!is_wp_error($terms) && !empty($terms)) {
                        foreach ($terms as $t) {
                            $vals[] = strtolower($t->slug);
                            $vals[] = strtolower($t->name);
                        }
                    }
                } else {
                    $opts = $attr->get_options();
                    if (!empty($opts)) {
                        foreach ($opts as $opt) {
                            $vals[] = strtolower(sanitize_title($opt));
                            $vals[] = strtolower($opt);
                        }
                    }
                }

                if (!empty($vals)) {
                    if (strpos($label, 'bean') !== false || strpos($label, 'type') !== false || strpos($name, 'bean') !== false || strpos($name, 'type') !== false) {
                        $beans_list = array_merge($beans_list, $vals);
                    }
                    if (strpos($label, 'process') !== false || strpos($label, 'proceso') !== false || strpos($label, 'processing') !== false || strpos($name, 'process') !== false || strpos($name, 'proceso') !== false) {
                        $proc_list = array_merge($proc_list, $vals);
                    }
                    if (strpos($label, 'brew') !== false || strpos($label, 'method') !== false || strpos($label, 'método') !== false || strpos($name, 'brew') !== false || strpos($name, 'method') !== false) {
                        $brew_list = array_merge($brew_list, $vals);
                    }
                }
            }
        }
    }

    if ($product_id > 0) {
        $terms = get_the_terms($product_id, 'product_cat');
        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $t) {
                $slug = strtolower($t->slug);
                $name = strtolower($t->name);
                $categories_list[] = $slug;
                $categories_list[] = $name;

                if (strpos($slug, 'espresso') !== false || strpos($name, 'espresso') !== false) { $beans_list[] = 'espresso'; $brew_list[] = 'espresso'; }
                if (strpos($slug, 'filter') !== false || strpos($name, 'filter') !== false || strpos($slug, 'filtro') !== false) { $beans_list[] = 'filter'; $brew_list[] = 'drip'; }
                if (strpos($slug, 'decaf') !== false || strpos($name, 'decaf') !== false || strpos($slug, 'descafeinado') !== false) { $beans_list[] = 'decaf'; }
            }
        }

        $tags = get_the_terms($product_id, 'product_tag');
        if (!empty($tags) && !is_wp_error($tags)) {
            foreach ($tags as $tag) {
                $slug = strtolower($tag->slug);
                $categories_list[] = $slug;
                if (strpos($slug, 'honey') !== false) $proc_list[] = 'honey';
                if (strpos($slug, 'wash') !== false || strpos($slug, 'lavado') !== false) $proc_list[] = 'washed';
                if (strpos($slug, 'natural') !== false) $proc_list[] = 'natural';
                if (strpos($slug, 'semi') !== false) $proc_list[] = 'semi-washed';

                if (strpos($slug, 'drip') !== false || strpos($slug, 'filter') !== false) $brew_list[] = 'drip';
                if (strpos($slug, 'espresso') !== false) $brew_list[] = 'espresso';
                if (strpos($slug, 'pour') !== false) $brew_list[] = 'pour-over';
                if (strpos($slug, 'french') !== false) $brew_list[] = 'french-press';
            }
        }

        $title_lower = strtolower($title);
        if (strpos($title_lower, 'espresso') !== false) { $beans_list[] = 'espresso'; $brew_list[] = 'espresso'; }
        if (strpos($title_lower, 'filter') !== false || strpos($title_lower, 'filtro') !== false) { $beans_list[] = 'filter'; $brew_list[] = 'drip'; }
        if (strpos($title_lower, 'decaf') !== false || strpos($title_lower, 'descafeinado') !== false) { $beans_list[] = 'decaf'; }
        if (strpos($title_lower, 'honey') !== false) { $proc_list[] = 'honey'; }
        if (strpos($title_lower, 'washed') !== false || strpos($title_lower, 'lavado') !== false) { $proc_list[] = 'washed'; }
        if (strpos($title_lower, 'natural') !== false) { $proc_list[] = 'natural'; }
        if (strpos($title_lower, 'semi-washed') !== false || strpos($title_lower, 'semi-lavado') !== false) { $proc_list[] = 'semi-washed'; }
    }

    $card_args = array(
        'product_id'  => $product_id,
        'title'       => $title,
        'permalink'   => $permalink,
        'price_html'  => $price_html,
        'image_html'  => $image_html,
        'is_sold_out' => $is_sold_out,
        'cat_attr'    => implode(' ', array_unique((array)$categories_list)),
        'beans_attr'  => implode(' ', array_unique((array)$beans_list)),
        'proc_attr'   => implode(' ', array_unique((array)$proc_list)),
        'brew_attr'   => implode(' ', array_unique((array)$brew_list)),
        'extra_class' => isset($args['class']) ? esc_attr($args['class']) : '',
    );

    get_template_part('template-parts/product-card', null, $card_args);
}


/* ==========================================================================

   10. AJAX ADD TO CART & REMOVE CART ITEM HANDLERS (SUPPORT VARIABLE PRODUCTS)
   ========================================================================== */
function designer_coffee_ajax_add_to_cart() {
    if (!class_exists('WooCommerce')) {
        wp_send_json_error(array('message' => 'WooCommerce is not active.'));
    }

    $product_id   = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity     = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
    $variation    = isset($_POST['variation']) ? (array) $_POST['variation'] : array();

    if ($product_id <= 0) {
        wp_send_json_error(array('message' => 'Invalid product ID.'));
    }

    // If product is variable but variation_id was not directly passed, match variation by size
    $product = wc_get_product($product_id);
    if ($product && $product->is_type('variable') && $variation_id <= 0) {
        $available_variations = $product->get_available_variations();
        $selected_size = isset($_POST['size']) ? sanitize_text_field($_POST['size']) : '';
        if (!empty($available_variations)) {
            foreach ($available_variations as $var) {
                if ($selected_size && isset($var['attributes'])) {
                    foreach ($var['attributes'] as $attr_val) {
                        if (strtolower((string)$attr_val) === strtolower($selected_size)) {
                            $variation_id = $var['variation_id'];
                            $variation = $var['attributes'];
                            break 2;
                        }
                    }
                }
            }
            if ($variation_id <= 0 && !empty($available_variations[0]['variation_id'])) {
                $variation_id = $available_variations[0]['variation_id'];
                $variation = $available_variations[0]['attributes'];
            }
        }
    }

    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation);
    if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation)) {
        do_action('woocommerce_ajax_added_to_cart', $product_id);

        ob_start();
        designer_coffee_header_cart();
        $cart_html = ob_get_clean();

        $count = WC()->cart->get_cart_contents_count();

        $fragments = array(
            'div.header-cart-wrapper' => $cart_html,
            '.header-cart-wrapper'    => $cart_html,
            '.cart-count-badge'       => '<span class="cart-count-badge cart-count">' . esc_html($count) . '</span>',
            '.cart-count'             => '<span class="cart-count-badge cart-count">' . esc_html($count) . '</span>',
        );
        $fragments = apply_filters('woocommerce_add_to_cart_fragments', $fragments);

        wp_send_json_success(array(
            'fragments' => $fragments,
            'cart_hash' => WC()->cart->get_cart_hash(),
            'count'     => $count,
        ));
    } else {
        $notices = wc_get_notices('error');
        $error_msg = !empty($notices) ? reset($notices)['notice'] : 'Failed to add product to cart.';
        wc_clear_notices();
        wp_send_json_error(array('message' => strip_tags($error_msg)));
    }
    wp_die();
}

add_action('wp_ajax_dc_add_to_cart', 'designer_coffee_ajax_add_to_cart');
add_action('wp_ajax_nopriv_dc_add_to_cart', 'designer_coffee_ajax_add_to_cart');

function designer_coffee_ajax_remove_cart_item() {
    if (!class_exists('WooCommerce')) {
        wp_send_json_error(array('message' => 'WooCommerce is not active.'));
    }

    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field($_POST['cart_item_key']) : '';

    if ($cart_item_key && WC()->cart->remove_cart_item($cart_item_key)) {
        ob_start();
        designer_coffee_header_cart();
        $cart_html = ob_get_clean();

        $count = WC()->cart->get_cart_contents_count();

        $fragments = array(
            'div.header-cart-wrapper' => $cart_html,
            '.header-cart-wrapper'    => $cart_html,
            '.cart-count-badge'       => '<span class="cart-count-badge cart-count">' . esc_html($count) . '</span>',
            '.cart-count'             => '<span class="cart-count-badge cart-count">' . esc_html($count) . '</span>',
        );

        $fragments = apply_filters('woocommerce_add_to_cart_fragments', $fragments);

        wp_send_json_success(array(
            'fragments' => $fragments,
            'cart_hash' => WC()->cart->get_cart_hash(),
            'count'     => $count,
        ));
    } else {
        wp_send_json_error(array('message' => 'Could not remove item.'));
    }
    wp_die();
}
add_action('wp_ajax_dc_remove_cart_item', 'designer_coffee_ajax_remove_cart_item');
add_action('wp_ajax_nopriv_dc_remove_cart_item', 'designer_coffee_ajax_remove_cart_item');
add_action('wp_ajax_dc_remove_from_cart', 'designer_coffee_ajax_remove_cart_item');
add_action('wp_ajax_nopriv_dc_remove_from_cart', 'designer_coffee_ajax_remove_cart_item');

function designer_coffee_ajax_update_cart_qty() {
    if (!class_exists('WooCommerce') || !WC()->cart) {
        wp_send_json_error(array('message' => 'WooCommerce is not active.'));
    }

    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field($_POST['cart_item_key']) : '';
    $quantity      = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if ($cart_item_key) {
        if ($quantity > 0) {
            WC()->cart->set_quantity($cart_item_key, $quantity, true);
        } else {
            WC()->cart->remove_cart_item($cart_item_key);
        }

        ob_start();
        designer_coffee_header_cart();
        $cart_html = ob_get_clean();

        $count = WC()->cart->get_cart_contents_count();

        $fragments = array(
            'div.header-cart-wrapper' => $cart_html,
            '.header-cart-wrapper'    => $cart_html,
            '.cart-count-badge'       => '<span class="cart-count-badge cart-count">' . esc_html($count) . '</span>',
            '.cart-count'             => '<span class="cart-count-badge cart-count">' . esc_html($count) . '</span>',
        );

        $fragments = apply_filters('woocommerce_add_to_cart_fragments', $fragments);

        wp_send_json_success(array(
            'fragments' => $fragments,
            'cart_hash' => WC()->cart->get_cart_hash(),
            'count'     => $count,
        ));
    } else {
        wp_send_json_error(array('message' => 'Invalid cart item key.'));
    }
    wp_die();
}
add_action('wp_ajax_dc_update_cart_qty', 'designer_coffee_ajax_update_cart_qty');
add_action('wp_ajax_nopriv_dc_update_cart_qty', 'designer_coffee_ajax_update_cart_qty');




/* ==========================================================================
   11. WOOCOMMERCE CURRENCY SYMBOL FORMATTING (CHANGE ₫ TO VND)
   ========================================================================== */
function designer_coffee_change_vnd_currency_symbol($currency_symbol, $currency) {
    if ($currency === 'VND' || $currency_symbol === '₫' || $currency_symbol === '&#8363;' || trim($currency_symbol) === '₫') {
        return ' VND';
    }
    return $currency_symbol;
}
add_filter('woocommerce_currency_symbol', 'designer_coffee_change_vnd_currency_symbol', 9999, 2);

function designer_coffee_format_vnd_price_string($formatted_price, $price, $args) {
    // Replace ₫ or &#8363; HTML entity with VND
    return str_replace(array('₫', '&#8363;', '&amp;#8363;'), ' VND', $formatted_price);
}
add_filter('wc_price', 'designer_coffee_format_vnd_price_string', 9999, 3);

/*
 * Ensure the theme's `woocommerce/checkout/review-order.php` is used on the
 * checkout page. Some setups (blocks, plugins) may bypass the default loader;
 * this hook removes the default and forces our template when viewing checkout.
 */
if (!function_exists('designer_coffee_force_review_template')) {
    add_action('wp', 'designer_coffee_force_review_template', 20);
    function designer_coffee_force_review_template() {
        if (!function_exists('is_checkout') || !is_checkout()) {
            return;
        }

        // Replace default review output with our theme template
        remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review', 10);
        add_action('woocommerce_checkout_order_review', 'designer_coffee_output_review_template', 10);
    }

    function designer_coffee_output_review_template() {
        $template = locate_template('woocommerce/checkout/review-order.php');
        if ($template && file_exists($template)) {
            include $template;
        } else {
            // Fallback to WooCommerce template loader
            wc_get_template('checkout/review-order.php');
        }
    }
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

/* ==========================================================================
   12. DYNAMIC WOOCOMMERCE SHOP FILTER OPTIONS QUERY (VIA WC ATTRIBUTES & TAXONOMIES)
   ========================================================================== */
function designer_coffee_get_shop_filter_terms($type = 'beans') {
    $options = array();

    if (!class_exists('WooCommerce')) {
        return $options;
    }

    // 1. Query WooCommerce Global Attribute Taxonomies via wc_get_attribute_taxonomies()
    $wc_attributes = function_exists('wc_get_attribute_taxonomies') ? wc_get_attribute_taxonomies() : array();
    $target_taxonomies = array();

    if (!empty($wc_attributes)) {
        foreach ($wc_attributes as $attr) {
            $tax_name   = wc_attribute_taxonomy_name($attr->attribute_name);
            $name_lower = strtolower($attr->attribute_name);
            $label_lower = strtolower($attr->attribute_label);

            if ($type === 'beans' && (strpos($name_lower, 'bean') !== false || strpos($name_lower, 'type') !== false || strpos($label_lower, 'bean') !== false || strpos($label_lower, 'category') !== false)) {
                $target_taxonomies[] = $tax_name;
            } elseif ($type === 'process' && (strpos($name_lower, 'process') !== false || strpos($name_lower, 'proceso') !== false || strpos($label_lower, 'process') !== false || strpos($label_lower, 'proceso') !== false)) {
                $target_taxonomies[] = $tax_name;
            } elseif ($type === 'brew' && (strpos($name_lower, 'brew') !== false || strpos($name_lower, 'method') !== false || strpos($name_lower, 'metodo') !== false || strpos($label_lower, 'brew') !== false || strpos($label_lower, 'method') !== false)) {
                $target_taxonomies[] = $tax_name;
            }
        }
    }

    // Default taxonomy fallbacks if not matched by label
    if (empty($target_taxonomies)) {
        if ($type === 'beans') {
            $target_taxonomies = array('pa_bean', 'pa_beans', 'pa_coffee_type', 'pa_type');
        } elseif ($type === 'process') {
            $target_taxonomies = array('pa_process', 'pa_processing', 'pa_proceso');
        } elseif ($type === 'brew') {
            $target_taxonomies = array('pa_brew_method', 'pa_brew', 'pa_method', 'pa_metodo');
        }
    }

    // Query terms for identified taxonomies
    if (!empty($target_taxonomies)) {
        $terms = get_terms(array(
            'taxonomy'   => $target_taxonomies,
            'hide_empty' => false,
        ));
        if (!is_wp_error($terms) && !empty($terms)) {
            foreach ($terms as $term) {
                $options[strtolower($term->slug)] = $term->name;
            }
        }
    }

    // For 'beans', also include product_cat categories (excluding generic coffee/merchandise)
    if ($type === 'beans') {
        $cats = get_terms(array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
        ));
        if (!is_wp_error($cats) && !empty($cats)) {
            foreach ($cats as $cat) {
                $slug = strtolower($cat->slug);
                if (in_array($slug, array('uncategorized', 'coffee', 'merchandise', 'merch', 'apparel', 'gear', 'accessories', 't-shirts', 'mugs'))) continue;
                $options[$slug] = $cat->name;
            }
        }
    }

    // Fallback: scan product attributes when no global terms are configured.
    if (empty($options)) {
        $products = wc_get_products(array('limit' => 50, 'status' => 'publish'));
        foreach ($products as $product) {
            foreach ($product->get_attributes() as $attr) {
                $label = wc_attribute_label($attr->get_name());
                $is_target = false;

                if ($type === 'beans' && (stripos($label, 'bean') !== false || stripos($label, 'category') !== false)) {
                    $is_target = true;
                } elseif ($type === 'process' && (stripos($label, 'process') !== false || stripos($label, 'proceso') !== false)) {
                    $is_target = true;
                } elseif ($type === 'brew' && (stripos($label, 'brew') !== false || stripos($label, 'method') !== false || stripos($label, 'método') !== false)) {
                    $is_target = true;
                }

                if (!$is_target) {
                    continue;
                }

                if ($attr->is_taxonomy()) {
                    $terms = wc_get_product_terms($product->get_id(), $attr->get_name(), array('fields' => 'all'));
                    if (!is_wp_error($terms)) {
                        foreach ($terms as $term) {
                            $options[strtolower($term->slug)] = $term->name;
                        }
                    }
                } else {
                    foreach ($attr->get_options() as $option) {
                        $options[strtolower(sanitize_title($option))] = $option;
                    }
                }
            }
        }
    }

    return $options;
}

/**
 * Custom Checkout Modifications for DESIGNER COFFEE
 */

// 1. Checkout assets are loaded from a separate file for independence
// Included below so the function can be managed independently.
if (file_exists(get_template_directory() . '/inc/checkout-assets.php')) {
    require_once get_template_directory() . '/inc/checkout-assets.php';
}

// 2. Default Billing & Shipping Country to Vietnam
if (!function_exists('designer_coffee_default_country')) {
    function designer_coffee_default_country($location) {
        $location['country'] = 'VN';
        return $location;
    }
    add_filter('woocommerce_customer_default_location_array', 'designer_coffee_default_country');
}

// 3. Translate Checkout Form Field Labels to Natural Vietnamese
if (!function_exists('designer_coffee_custom_checkout_fields')) {
    function designer_coffee_custom_checkout_fields($fields) {
        $fields['billing']['billing_email']['label']       = __('Email', 'designer-coffee');
        $fields['billing']['billing_first_name']['label']  = __('Tên', 'designer-coffee');
        $fields['billing']['billing_last_name']['label']   = __('Họ', 'designer-coffee');
        $fields['billing']['billing_company']['label']     = __('Công ty (không bắt buộc)', 'designer-coffee');
        $fields['billing']['billing_address_1']['label']   = __('Địa chỉ', 'designer-coffee');
        $fields['billing']['billing_address_2']['label']   = __('Căn hộ, số nhà, tầng, v.v. (không bắt buộc)', 'designer-coffee');
        $fields['billing']['billing_postcode']['label']    = __('Mã bưu điện', 'designer-coffee');
        $fields['billing']['billing_city']['label']        = __('Tỉnh / Thành phố', 'designer-coffee');
        $fields['billing']['billing_state']['label']       = __('Quận / Huyện', 'designer-coffee');
        $fields['billing']['billing_phone']['label']       = __('Số điện thoại', 'designer-coffee');

        // Priority ordering for a clean 2-column input layout
        $fields['billing']['billing_email']['priority']      = 5;
        $fields['billing']['billing_last_name']['priority']  = 10;
        $fields['billing']['billing_first_name']['priority'] = 20;

        return $fields;
    }
    add_filter('woocommerce_checkout_fields', 'designer_coffee_custom_checkout_fields');
}

// 4. Change Submit Button Text to "ĐẶT HÀNG"
if (!function_exists('designer_coffee_order_button_text')) {
    function designer_coffee_order_button_text() {
        return __('ĐẶT HÀNG', 'designer-coffee');
    }
    add_filter('woocommerce_order_button_text', 'designer_coffee_order_button_text');
}

// 5. Add Product Thumbnail to Order Summary
if (!function_exists('designer_coffee_cart_item_image')) {
    function designer_coffee_cart_item_image($product_name, $cart_item, $cart_item_key) {
        if (!is_checkout()) {
            return $product_name;
        }

        $_product = $cart_item['data'];
        $thumbnail = $_product->get_image(array(48, 48), array('class' => 'designer-cart-product-img'));

        return '<div class="designer-cart-item-row">' . $thumbnail . '<div class="designer-cart-item-details"><span class="designer-cart-product-title">' . $product_name . '</span>';
    }
    add_filter('woocommerce_cart_item_name', 'designer_coffee_cart_item_image', 10, 3);
}

// 6. Display Coffee Grind Meta in Summary
if (!function_exists('designer_coffee_checkout_item_quantity')) {
    function designer_coffee_checkout_item_quantity($quantity_html, $cart_item, $cart_item_key) {
        $meta_html = '';
        
        $grind_val = !empty($cart_item['cgs_grind']) ? $cart_item['cgs_grind'] : (!empty($cart_item['grind']) ? $cart_item['grind'] : '');
        if ($grind_val) {
            $cgs_opts = function_exists('cgs_get_grind_options') ? cgs_get_grind_options() : array();
            $label_val = isset($cgs_opts[$grind_val]['label']) ? $cgs_opts[$grind_val]['label'] : ucfirst(str_replace('-', ' ', $grind_val));
            $meta_html .= '<div class="designer-grind-meta">Grind: <strong class="designer-grind-value">' . esc_html($label_val) . '</strong></div>';
        } elseif (!empty($cart_item['variation']) && is_array($cart_item['variation'])) {
            foreach ($cart_item['variation'] as $key => $value) {
                if (strpos(strtolower($key), 'grind') !== false || strpos(strtolower($key), 'xay') !== false) {
                    $meta_html .= '<div class="designer-grind-meta">' . esc_html(wc_attribute_label(str_replace('attribute_', '', $key))) . ': <strong class="designer-grind-value">' . esc_html($value) . '</strong></div>';
                }
            }
        }

        return $meta_html . '</div></div>' . $quantity_html;
    }
    add_filter('woocommerce_checkout_cart_item_quantity', 'designer_coffee_checkout_item_quantity', 10, 3);
}

    // Ensure grind/meta values returned by WooCommerce include a readable class
    if (!function_exists('designer_coffee_wrap_grind_in_cart_item_data')) {
        function designer_coffee_wrap_grind_in_cart_item_data($item_data, $cart_item) {
            if (!empty($item_data) && is_array($item_data)) {
                foreach ($item_data as &$d) {
                    $key = isset($d['key']) ? $d['key'] : '';
                    $value = isset($d['value']) ? $d['value'] : '';
                    if (stripos($key, 'grind') !== false || stripos($value, 'grind') !== false || stripos($value, 'xay') !== false) {
                        $d['value'] = '<span class="designer-grind-value">' . $d['value'] . '</span>';
                    }
                }
            }
            return $item_data;
        }
        add_filter('woocommerce_get_item_data', 'designer_coffee_wrap_grind_in_cart_item_data', 10, 2);
    }

    if (!function_exists('designer_coffee_wrap_grind_in_order_meta')) {
        function designer_coffee_wrap_grind_in_order_meta($formatted_meta, $item) {
            if (!empty($formatted_meta) && is_array($formatted_meta)) {
                foreach ($formatted_meta as $id => $meta) {
                    // meta can be object or array; handle both
                    $display_key = is_object($meta) ? (isset($meta->display_key) ? $meta->display_key : '') : (isset($meta['display_key']) ? $meta['display_key'] : (isset($meta['key']) ? $meta['key'] : ''));
                    $display_value = is_object($meta) ? (isset($meta->display_value) ? $meta->display_value : '') : (isset($meta['display_value']) ? $meta['display_value'] : (isset($meta['value']) ? $meta['value'] : ''));
                    if (stripos($display_key, 'grind') !== false || stripos($display_value, 'grind') !== false || stripos($display_value, 'xay') !== false) {
                        if (is_object($meta)) {
                            $meta->display_value = '<span class="designer-grind-value">' . $meta->display_value . '</span>';
                            $formatted_meta[$id] = $meta;
                        } elseif (is_array($meta)) {
                            $meta['display_value'] = '<span class="designer-grind-value">' . $display_value . '</span>';
                            $formatted_meta[$id] = $meta;
                        }
                    }
                }
            }
            return $formatted_meta;
        }
        add_filter('woocommerce_order_item_get_formatted_meta_data', 'designer_coffee_wrap_grind_in_order_meta', 10, 2);
    }

// 7. Translate Checkout Section Headings & Common Labels to Vietnamese
if (!function_exists('designer_coffee_translate_checkout_titles')) {
    function designer_coffee_translate_checkout_titles($translated_text, $text, $domain) {
        if (is_checkout() && !is_order_received_page()) {
            switch ($text) {
                case 'Customer details':
                    return 'Thông tin liên hệ';
                case 'Billing details':
                case 'Billing Details':
                    return 'Thông tin nhận hàng';
                case 'Shipping details':
                case 'Shipping Details':
                    return 'Địa chỉ nhận hàng';
                case 'Your order':
                case 'Your Order':
                    return 'Đơn hàng';
                case 'Subtotal':
                    return 'Tạm tính';
                case 'Shipping':
                    return 'Phí vận chuyển';
                case 'Total':
                    return 'Tổng cộng';
                case 'Direct bank transfer':
                    return 'Chuyển khoản ngân hàng';
                case 'Cash on delivery':
                    return 'Thanh toán khi nhận hàng (COD)';
            }
        }
        return $translated_text;
    }
    add_filter('gettext', 'designer_coffee_translate_checkout_titles', 20, 3);
}
