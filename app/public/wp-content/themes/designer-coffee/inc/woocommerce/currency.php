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


