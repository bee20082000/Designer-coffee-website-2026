<?php
/**
 * Single checkout payment method.
 *
 * @package DesignerCoffee
 * @version 3.5.0
 */

defined('ABSPATH') || exit;

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
