<?php
/**
 * Designer Coffee theme bootstrap.
 *
 * Keep this file limited to loading focused modules. WordPress loads it
 * automatically before rendering theme templates.
 *
 * @package DesignerCoffee
 */

if (!defined('ABSPATH')) {
    exit;
}

$designer_coffee_modules = array(
    'inc/theme-setup.php',
    'inc/assets.php',
    'inc/acf-fields.php',
    'inc/admin.php',
    'inc/woocommerce/cart.php',
    'inc/woocommerce/catalog.php',
    'inc/woocommerce/currency.php',
    'inc/woocommerce/permalinks.php',
    'inc/woocommerce/checkout.php',
);

foreach ($designer_coffee_modules as $designer_coffee_module) {
    require_once get_template_directory() . '/' . $designer_coffee_module;
}

unset($designer_coffee_module, $designer_coffee_modules);
