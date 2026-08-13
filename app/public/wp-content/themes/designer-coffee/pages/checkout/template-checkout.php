<?php
/**
 * Custom Checkout Page Template
 *
 * @package DesignerCoffee
 */

if (!defined('ABSPATH')) {
    exit;
}

// Route to Thank You template on Order Received endpoint
if (function_exists('is_order_received_page') && (is_order_received_page() || is_wc_endpoint_url('order-received'))) {
    include get_template_directory() . '/pages/checkout/template-thankyou.php';
    return;
}

get_header();



$is_woo = class_exists('WooCommerce') && function_exists('WC') && WC()->cart;
$cart = $is_woo ? WC()->cart : null;

if ($cart) {
    $cart->calculate_totals();
    $count = $cart->get_cart_contents_count();
    $is_empty = $cart->is_empty() || $count === 0;
} else {
    $count = 0;
    $is_empty = true;
}

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');
?>

<main id="primary" class="site-main designer-checkout-wrapper">
    <div class="designer-checkout-container">
        <?php if ($is_empty) : ?>
            <header class="designer-checkout-header">
                <h1 class="designer-checkout-title">THANH TOÁN ĐƠN HÀNG</h1>
            </header>

            <div class="designer-checkout-empty">
                <div class="checkout-empty-icon">
                    <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                </div>
                <h2>Giỏ hàng của bạn đang trống</h2>
                <p>Hiện chưa có sản phẩm nào trong giỏ hàng để tiến hành thanh toán. Hãy khám phá các dòng cà phê độc đáo của Designer Coffee.</p>
                <a href="<?php echo esc_url($shop_url); ?>" class="designer-checkout-btn">
                    KHÁM PHÁ CỬA HÀNG &rarr;
                </a>
            </div>
        <?php else : ?>
            <header class="designer-checkout-header">
                <a class="designer-checkout-back" href="<?php echo esc_url($shop_url); ?>">&larr; Tiếp tục mua sắm</a>
                <div>
                    <span class="designer-checkout-eyebrow">Designer Coffee</span>
                    <h1 class="designer-checkout-title">Thanh toán</h1>
                    <p class="designer-checkout-subtitle">Hoàn tất đơn hàng của bạn một cách an toàn.</p>
                </div>
            </header>

            <div class="checkout-notices-wrapper">
                <?php wc_print_notices(); ?>
            </div>

            <div class="designer-checkout-form-wrapper">
                <?php
                if (class_exists('WC_Shortcodes') && method_exists('WC_Shortcodes', 'checkout')) {
                    echo WC_Shortcodes::checkout(array());
                } elseif (function_exists('wc_get_template') && WC()->checkout()) {
                    wc_get_template('checkout/form-checkout.php', array('checkout' => WC()->checkout()));
                } else {
                    echo do_shortcode('[woocommerce_checkout]');
                }
                ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
