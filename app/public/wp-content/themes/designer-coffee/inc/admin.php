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


