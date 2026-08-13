<?php
/**
 * Checkout payment section.
 *
 * Uses WooCommerce's live gateway collection while providing theme-owned
 * presentation. Keep hooks and field names aligned with the core template.
 *
 * @package DesignerCoffee
 * @version 10.9.0
 */

defined('ABSPATH') || exit;

if (!wp_doing_ajax()) {
    do_action('woocommerce_review_order_before_payment');
}
?>
<section id="payment" class="woocommerce-checkout-payment dc-payment-panel" aria-labelledby="dc-payment-title">
    <header class="dc-payment-header">
        <h3 id="dc-payment-title"><?php esc_html_e('Payment', 'woocommerce'); ?></h3>
    </header>

    <?php if (WC()->cart && WC()->cart->needs_payment()) : ?>
        <ul class="wc_payment_methods payment_methods methods dc-payment-methods" aria-label="<?php esc_attr_e('Payment methods', 'woocommerce'); ?>">
            <?php if (!empty($available_gateways)) : ?>
                <?php foreach ($available_gateways as $gateway) : ?>
                    <?php
                    $gateway_id          = $gateway->id;
                    $gateway_title       = $gateway->get_title();
                    $gateway_has_details = $gateway->has_fields();
                    ?>
                    <li class="wc_payment_method payment_method_<?php echo esc_attr($gateway_id); ?> dc-payment-method<?php echo $gateway->chosen ? ' is-selected' : ''; ?>">
                        <input
                            id="payment_method_<?php echo esc_attr($gateway_id); ?>"
                            type="radio"
                            class="input-radio"
                            name="payment_method"
                            value="<?php echo esc_attr($gateway_id); ?>"
                            <?php checked($gateway->chosen, true); ?>
                            data-order_button_text="<?php echo esc_attr($gateway->order_button_text); ?>"
                        >

                        <label class="dc-payment-method-label" for="payment_method_<?php echo esc_attr($gateway_id); ?>">
                            <span class="dc-payment-radio" aria-hidden="true"></span>
                            <span class="dc-payment-method-copy">
                                <strong><?php echo wp_kses_post($gateway_title); ?></strong>
                            </span>
                            <?php if ($gateway->get_icon()) : ?>
                                <span class="dc-payment-method-icon"><?php echo wp_kses_post($gateway->get_icon()); ?></span>
                            <?php endif; ?>
                        </label>

                        <?php if ($gateway_has_details) : ?>
                            <div class="payment_box payment_method_<?php echo esc_attr($gateway_id); ?> dc-payment-details"<?php echo $gateway->chosen ? '' : ' style="display:none;"'; ?>>
                                <?php $gateway->payment_fields(); ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php else : ?>
                <li class="dc-payment-empty">
                    <?php
                    $message = WC()->customer->get_billing_country()
                        ? __('Sorry, it seems that there are no available payment methods. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce')
                        : __('Please fill in your details above to see available payment methods.', 'woocommerce');

                    wc_print_notice(
                        apply_filters('woocommerce_no_available_payment_methods_message', $message),
                        'notice'
                    );
                    ?>
                </li>
            <?php endif; ?>
        </ul>
    <?php endif; ?>

    <div class="form-row place-order dc-place-order">
        <noscript>
            <?php
            printf(
                esc_html__('Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce'),
                '<em>',
                '</em>'
            );
            ?>
            <br>
            <button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e('Update totals', 'woocommerce'); ?>">
                <?php esc_html_e('Update totals', 'woocommerce'); ?>
            </button>
        </noscript>

        <?php wc_get_template('checkout/terms.php'); ?>
        <?php do_action('woocommerce_review_order_before_submit'); ?>

        <?php
        echo apply_filters(
            'woocommerce_order_button_html',
            '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '"><span>' . esc_html($order_button_text) . '</span><span aria-hidden="true">→</span></button>'
        ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>

        <?php do_action('woocommerce_review_order_after_submit'); ?>
        <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
    </div>
</section>
<?php
if (!wp_doing_ajax()) {
    do_action('woocommerce_review_order_after_payment');
}
