<?php
/**
 * Custom Thank You / Order Received Page Template — DESIGNER COFFEE
 *
 * @package DesignerCoffee
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wp;
$order_id = isset($wp->query_vars['order-received']) ? absint($wp->query_vars['order-received']) : 0;
$order_key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';

$order = false;
if ($order_id > 0) {
    $order = wc_get_order($order_id);
    if ($order && $order_key && !hash_equals($order->get_order_key(), $order_key)) {
        $order = false;
    }
}

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');

get_header();
?>

<main id="primary" class="site-main designer-thankyou-wrapper">
    <div class="designer-thankyou-container">
        <!-- Step Navigation Breadcrumb -->
        <nav class="designer-step-nav" aria-label="Checkout Progress">
            <button type="button" class="step-item step-completed" onclick="document.querySelector('.header-cart-wrapper')?.classList.add('is-open');">
                <span class="step-num">&#10003;</span>
                <span class="step-text">Giỏ hàng</span>
            </button>
            <span class="step-divider">&rsaquo;</span>
            <div class="step-item step-completed">
                <span class="step-num">&#10003;</span>
                <span class="step-text">Thanh toán</span>
            </div>
            <span class="step-divider">&rsaquo;</span>
            <div class="step-item step-active">
                <span class="step-num">3</span>
                <span class="step-text">Hoàn tất</span>
            </div>
        </nav>

        <header class="designer-thankyou-header">
            <h1 class="designer-thankyou-title">HOÀN TẤT ĐƠN HÀNG</h1>
        </header>

        <?php if ($order) : ?>
            <div class="thankyou-card-hero">
                <div class="thankyou-check-badge">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <h2 class="thankyou-heading">CẢM ƠN BẠN ĐÃ ĐẶT HÀNG!</h2>
                <p class="thankyou-subtext">Đơn hàng <strong>#<?php echo esc_html($order->get_order_number()); ?></strong> của bạn đã được tiếp nhận và đang được xử lý.</p>
            </div>

            <!-- Key Order Overview Bar -->
            <div class="thankyou-overview-grid">
                <div class="overview-item">
                    <span class="overview-label">MÃ ĐƠN HÀNG</span>
                    <strong class="overview-value">#<?php echo esc_html($order->get_order_number()); ?></strong>
                </div>
                <div class="overview-item">
                    <span class="overview-label">NGÀY ĐẶT</span>
                    <strong class="overview-value"><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></strong>
                </div>
                <div class="overview-item">
                    <span class="overview-label">TỔNG CỘNG</span>
                    <strong class="overview-value price"><?php echo $order->get_formatted_order_total(); ?></strong>
                </div>
                <div class="overview-item">
                    <span class="overview-label">PHƯƠNG THỨC THANH TOÁN</span>
                    <strong class="overview-value"><?php echo esc_html($order->get_payment_method_title()); ?></strong>
                </div>
            </div>

            <!-- BACS Direct Bank Transfer Details -->
            <?php
            if ($order->get_payment_method() === 'bacs') {
                $bacs_account_options = get_option('woocommerce_bacs_accounts', array());
                if (!empty($bacs_account_options)) {
                    ?>
                    <div class="thankyou-bacs-card">
                        <h3 class="bacs-title">THÔNG TIN CHUYỂN KHOẢN NGÂN HÀNG</h3>
                        <p class="bacs-intro">Vui lòng chuyển khoản tới thông tin dưới đây để đơn hàng được chuẩn bị & giao nhanh nhất:</p>
                        
                        <div class="bacs-accounts-list">
                            <?php foreach ($bacs_account_options as $account) : ?>
                                <div class="bacs-account-row">
                                    <?php if (!empty($account['bank_name'])) : ?>
                                        <div class="bacs-field">
                                            <span class="bacs-label">Ngân hàng:</span>
                                            <strong class="bacs-val"><?php echo esc_html($account['bank_name']); ?></strong>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($account['account_name'])) : ?>
                                        <div class="bacs-field">
                                            <span class="bacs-label">Tên tài khoản:</span>
                                            <strong class="bacs-val"><?php echo esc_html($account['account_name']); ?></strong>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($account['account_number'])) : ?>
                                        <div class="bacs-field highlight">
                                            <span class="bacs-label">Số tài khoản:</span>
                                            <strong class="bacs-val"><?php echo esc_html($account['account_number']); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="bacs-memo-box">
                            <span>Nội dung chuyển khoản:</span>
                            <strong>DH #<?php echo esc_html($order->get_order_number()); ?></strong>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>

            <!-- Itemized Order Table -->
            <div class="thankyou-details-section">
                <h3 class="thankyou-section-heading">CHI TIẾT ĐƠN HÀNG</h3>
                
                <table class="thankyou-order-table">
                    <thead>
                        <tr>
                            <th>SẢN PHẨM</th>
                            <th class="text-right">TỔNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order->get_items() as $item_id => $item) : ?>
                            <?php
                            $_product = $item->get_product();
                            $thumbnail = $_product ? $_product->get_image(array(50, 50), array('class' => 'thankyou-item-img')) : '';
                            $item_meta = wc_display_item_meta($item, array('echo' => false));
                            ?>
                            <tr>
                                <td class="thankyou-product-cell">
                                    <div class="thankyou-product-flex">
                                        <?php if ($thumbnail) echo $thumbnail; ?>
                                        <div class="thankyou-product-info">
                                            <span class="thankyou-product-name"><?php echo esc_html($item->get_name()); ?></span>
                                            <span class="thankyou-product-qty">Số lượng: <?php echo esc_html($item->get_quantity()); ?></span>
                                            <?php if ($item_meta) echo '<div class="thankyou-item-meta">' . $item_meta . '</div>'; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right thankyou-product-total">
                                    <?php echo $order->get_formatted_line_subtotal($item); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <?php foreach ($order->get_order_item_totals() as $key => $total) : ?>
                            <tr>
                                <th scope="row"><?php echo esc_html($total['label']); ?></th>
                                <td class="text-right"><?php echo $total['value']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tfoot>
                </table>
            </div>

            <!-- Customer Addresses Grid -->
            <div class="thankyou-addresses-grid">
                <?php if ($order->get_formatted_shipping_address()) : ?>
                    <div class="address-card">
                        <h4 class="address-title">ĐỊA CHỈ GIAO HÀNG</h4>
                        <address><?php echo wp_kses_post($order->get_formatted_shipping_address()); ?></address>
                        <?php if ($order->get_billing_phone()) : ?>
                            <p class="address-phone">SĐT: <?php echo esc_html($order->get_billing_phone()); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="address-card">
                    <h4 class="address-title">THÔNG TIN XÁC NHẬN</h4>
                    <p>Email: <strong><?php echo esc_html($order->get_billing_email()); ?></strong></p>
                    <p>Họ tên: <strong><?php echo esc_html($order->get_formatted_billing_full_name()); ?></strong></p>
                </div>
            </div>

            <!-- Continue Shopping Action -->
            <div class="thankyou-cta-row">
                <a href="<?php echo esc_url($shop_url); ?>" class="thankyou-shop-btn">TIẾP TỤC MUA SẮM &rarr;</a>
            </div>

        <?php else : ?>
            <div class="designer-thankyou-empty">
                <h2>Cảm ơn bạn đã đặt hàng!</h2>
                <p>Đơn hàng của bạn đã được lưu thành công.</p>
                <a href="<?php echo esc_url($shop_url); ?>" class="thankyou-shop-btn">TIẾP TỤC MUA SẮM &rarr;</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
