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

function designer_coffee_verify_cart_ajax_request() {
    check_ajax_referer('designer_coffee_cart', 'nonce');
}

function designer_coffee_get_cart_ajax_response() {
    ob_start();
    designer_coffee_header_cart();
    $cart_html = ob_get_clean();
    $count = WC()->cart->get_cart_contents_count();

    $fragments = apply_filters('woocommerce_add_to_cart_fragments', array(
        'div.header-cart-wrapper' => $cart_html,
        '.header-cart-wrapper'    => $cart_html,
        '.cart-count-badge'       => '<span class="cart-count-badge cart-count">' . esc_html($count) . '</span>',
        '.cart-count'             => '<span class="cart-count-badge cart-count">' . esc_html($count) . '</span>',
    ));

    return array(
        'fragments' => $fragments,
        'cart_hash' => WC()->cart->get_cart_hash(),
        'count'     => $count,
    );
}

/* ==========================================================================
   AJAX ADD TO CART & REMOVE CART ITEM HANDLERS (SUPPORT VARIABLE PRODUCTS)
   ========================================================================== */
function designer_coffee_ajax_add_to_cart() {
    designer_coffee_verify_cart_ajax_request();

    if (!class_exists('WooCommerce')) {
        wp_send_json_error(array('message' => 'WooCommerce is not active.'));
    }

    $product_id   = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity     = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
    $variation    = isset($_POST['variation']) ? array_map('wc_clean', (array) wp_unslash($_POST['variation'])) : array();

    if ($product_id <= 0) {
        wp_send_json_error(array('message' => 'Invalid product ID.'));
    }

    // If product is variable but variation_id was not directly passed, match variation by size
    $product = wc_get_product($product_id);
    if ($product && $product->is_type('variable') && $variation_id <= 0) {
        $available_variations = $product->get_available_variations();
        $selected_size = isset($_POST['size']) ? sanitize_text_field(wp_unslash($_POST['size'])) : '';
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

        wp_send_json_success(designer_coffee_get_cart_ajax_response());
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
    designer_coffee_verify_cart_ajax_request();

    if (!class_exists('WooCommerce')) {
        wp_send_json_error(array('message' => 'WooCommerce is not active.'));
    }

    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash($_POST['cart_item_key'])) : '';

    if ($cart_item_key && WC()->cart->remove_cart_item($cart_item_key)) {
        wp_send_json_success(designer_coffee_get_cart_ajax_response());
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
    designer_coffee_verify_cart_ajax_request();

    if (!class_exists('WooCommerce') || !WC()->cart) {
        wp_send_json_error(array('message' => 'WooCommerce is not active.'));
    }

    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash($_POST['cart_item_key'])) : '';
    $quantity      = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if ($cart_item_key) {
        if ($quantity > 0) {
            WC()->cart->set_quantity($cart_item_key, $quantity, true);
        } else {
            WC()->cart->remove_cart_item($cart_item_key);
        }

        wp_send_json_success(designer_coffee_get_cart_ajax_response());
    } else {
        wp_send_json_error(array('message' => 'Invalid cart item key.'));
    }
    wp_die();
}
add_action('wp_ajax_dc_update_cart_qty', 'designer_coffee_ajax_update_cart_qty');
add_action('wp_ajax_nopriv_dc_update_cart_qty', 'designer_coffee_ajax_update_cart_qty');



