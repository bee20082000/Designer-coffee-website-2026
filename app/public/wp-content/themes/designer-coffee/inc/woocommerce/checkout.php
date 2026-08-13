<?php
/**
 * Theme module.
 *
 * @package DesignerCoffee
 */

if (!defined('ABSPATH')) {
    exit;
}

function designer_coffee_enqueue_checkout_assets() {
    $is_checkout = function_exists('is_checkout') && is_checkout();
    $is_thankyou = function_exists('is_order_received_page') && (
        is_order_received_page() ||
        (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-received'))
    );

    $is_checkout_template = function_exists('is_page_template') && is_page_template('pages/checkout/template-checkout.php');
    $is_thankyou_template = function_exists('is_page_template') && is_page_template('pages/checkout/template-thankyou.php');
    $is_checkout_page = function_exists('is_page') && is_page('checkout');

    if ($is_checkout || $is_checkout_template || $is_checkout_page) {
        wp_enqueue_style(
            'designer-coffee-checkout-css',
            get_template_directory_uri() . '/pages/checkout/checkout.css',
            array('dc-global'),
            designer_coffee_asset_version('pages/checkout/checkout.css')
        );
        wp_enqueue_style(
            'designer-coffee-review-order-css',
            get_template_directory_uri() . '/pages/checkout/review-order.css',
            array('designer-coffee-checkout-css'),
            designer_coffee_asset_version('pages/checkout/review-order.css')
        );
    }

    if ($is_thankyou || $is_thankyou_template) {
        wp_enqueue_style(
            'designer-coffee-thankyou-css',
            get_template_directory_uri() . '/pages/checkout/thankyou.css',
            array('dc-global'),
            designer_coffee_asset_version('pages/checkout/thankyou.css')
        );
    }
}
add_action('wp_enqueue_scripts', 'designer_coffee_enqueue_checkout_assets', 5);

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


/**
 * Custom Checkout Modifications for DESIGNER COFFEE
 */

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
