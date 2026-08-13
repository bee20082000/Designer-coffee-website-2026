<?php
/**
 * Custom compact review order table
 * Overridden in the theme to produce a smaller, cleaner checkout summary
 * Path: yourtheme/woocommerce/checkout/review-order.php
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- THEME_OVERRIDE: designer-coffee/woocommerce/checkout/review-order.php -->
<table class="shop_table woocommerce-checkout-review-order-table dc-compact-order-table">
  <thead>
    <tr>
      <th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
      <th class="product-total"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php do_action( 'woocommerce_review_order_before_cart_contents' ); ?>

    <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
      $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
      $visible    = apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key );

      if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && $visible ) :
        $product_name = $_product->get_name();
        $thumbnail    = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( array( 144, 144 ), array( 'class' => 'dc-order-product-image' ) ), $cart_item, $cart_item_key );
        $item_data    = wc_get_formatted_cart_item_data( $cart_item );
        $subtotal     = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
    ?>
      <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
        <td class="product-name">
          <div class="dc-order-product">
            <div class="dc-order-product-media">
              <?php echo wp_kses_post( $thumbnail ); ?>
              <span class="dc-qty-badge" aria-label="<?php echo esc_attr( sprintf( __( 'Quantity: %d', 'woocommerce' ), $cart_item['quantity'] ) ); ?>"><?php echo esc_html( $cart_item['quantity'] ); ?></span>
            </div>
            <div class="dc-order-product-copy">
              <span class="dc-order-product-name"><?php echo wp_kses_post( $product_name ); ?></span>
              <?php if ( $item_data ) : ?>
                <div class="dc-compact-meta"><?php echo wp_kses_post( $item_data ); ?></div>
              <?php endif; ?>
            </div>
          </div>
        </td>
        <td class="product-total" style="text-align:right;vertical-align:middle;">
          <?php echo wp_kses_post( $subtotal ); ?>
        </td>
      </tr>
    <?php endif; endforeach; ?>

    <?php do_action( 'woocommerce_review_order_after_cart_contents' ); ?>
  </tbody>
  <tfoot>
    <tr class="cart-subtotal">
      <th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
      <td colspan="2" style="text-align:right"><?php wc_cart_totals_subtotal_html(); ?></td>
    </tr>

    <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
      <tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
        <th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
        <td colspan="2" style="text-align:right"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
      </tr>
    <?php endforeach; ?>

    <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
      <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
      <?php wc_cart_totals_shipping_html(); ?>
      <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
    <?php endif; ?>

    <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
      <tr class="fee">
        <th><?php echo esc_html( $fee->name ); ?></th>
        <td colspan="2" style="text-align:right"><?php wc_cart_totals_fee_html( $fee ); ?></td>
      </tr>
    <?php endforeach; ?>

    <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
      <?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
        <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
          <tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
            <th><?php echo esc_html( $tax->label ); ?></th>
            <td colspan="2" style="text-align:right"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else : ?>
        <tr class="tax-total">
          <th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
          <td colspan="2" style="text-align:right"><?php wc_cart_totals_taxes_total_html(); ?></td>
        </tr>
      <?php endif; ?>
    <?php endif; ?>

    <?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

    <tr class="order-total">
      <th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
      <td colspan="2" style="text-align:right"><?php wc_cart_totals_order_total_html(); ?></td>
    </tr>

    <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
  </tfoot>
</table>
