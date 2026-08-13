<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <!-- Google Fonts: Google Sans, Google Sans Code & Schoolbell -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Code:ital,wght,MONO@0,300..800,1;1,300..800,1&family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&family=Schoolbell&display=swap" rel="stylesheet">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container nav-container">
        <!-- BRAND LOGO LEFT (EXACT LOGO.SVG FROM FILE) -->
        <a href="<?php echo esc_url(home_url('/')); ?>" class="brand-logo" aria-label="<?php bloginfo('name'); ?>">
            <?php
            $svg_file = ABSPATH . 'wp-content/uploads/Logo/Logo.svg';
            if (file_exists($svg_file)) {
                echo file_get_contents($svg_file);
            }
            ?>
        </a>

        <!-- RIGHT GROUP: QUICK SHOP, CART, MENU -->
        <div class="header-right-group">
            <!-- CART WITH HOVER MINI-CART DROPDOWN -->
            <?php
            if (function_exists('designer_coffee_header_cart')) {
                designer_coffee_header_cart();
            } else {
                $count = (class_exists('WooCommerce') && function_exists('WC') && WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
                echo '<button type="button" class="nav-cart-text" aria-label="Shopping Cart">Cart (' . esc_html($count) . ')</button>';
            }
            ?>

            <!-- CRAV STYLE SOLID RED MENU CARD DROPDOWN -->
            <div class="menu-dropdown-wrapper">
                <button class="menu-dropdown-trigger" type="button" aria-label="Toggle Menu" aria-haspopup="true" aria-expanded="false">
                    <svg width="18" height="14" viewBox="0 0 18 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 1.5H18M0 7H18M0 12.5H18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                    </svg>
                </button>


                <div class="menu-dropdown-panel">
                    <nav class="main-nav" aria-label="Main Navigation">
                        <?php
                        if (has_nav_menu('primary')) {
                            wp_nav_menu(array(
                                'theme_location' => 'primary',
                                'container'      => false,
                                'depth'          => 1,
                            ));
                        } else {
                            $shop_url    = class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/shop');
                            $blog_url    = get_option('page_for_posts') ? get_permalink(get_option('page_for_posts')) : home_url('/blog');
                            $about_url   = home_url('/about');
                            $contact_url = home_url('/contact');
                            
                            $is_home_active    = is_front_page();
                            $is_shop_active    = class_exists('WooCommerce') && (is_shop() || is_product() || is_product_category());
                            $is_about_active   = is_page('about');
                            $is_contact_active = is_page('contact');
                            $is_blog_active    = (is_home() || is_single()) && !$is_shop_active;

                            echo '<ul>';
                            echo '<li><a href="' . esc_url(home_url('/')) . '" class="' . ($is_home_active ? 'active' : '') . '">Home</a></li>';
                            echo '<li><a href="' . esc_url($shop_url) . '" class="' . ($is_shop_active ? 'active' : '') . '">Shop</a></li>';
                            echo '<li><a href="' . esc_url($about_url) . '" class="' . ($is_about_active ? 'active' : '') . '">About</a></li>';
                            echo '<li><a href="' . esc_url($contact_url) . '" class="' . ($is_contact_active ? 'active' : '') . '">Contact</a></li>';
                            echo '<li><a href="' . esc_url($blog_url) . '" class="' . ($is_blog_active ? 'active' : '') . '">Blog</a></li>';
                            echo '</ul>';
                        }
                        ?>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
