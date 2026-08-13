<?php
/**
 * Template Name: Shop Page Template
 * Location: pages/shop/template-shop.php
 *
 * Modern Coffee Shop Page with Full-Width 21:9 Cover Photo Hero Banner,
 * Centered Title Overlay, and Category Filter Tabs (Beans, Merchandise).
 *
 * @package DesignerCoffee
 */

get_header();

// Determine page title
if (function_exists('is_product_category') && is_product_category()) {
    $page_title = single_term_title('', false);
} elseif (function_exists('is_product_tag') && is_product_tag()) {
    $page_title = single_term_title('', false);
} else {
    $page_title = 'What do you want to buy, my love?';
}

$active_cat = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'all';
$cover_img_url = home_url('/wp-content/uploads/2026/02/28.jpg');
?>

<div class="shop-page-wrapper section">
    <div class="container">
        
        <!-- Hero Cover Photo Banner with Centered Title Overlay -->
        <div class="shop-cover-banner">
            <img src="<?php echo esc_url($cover_img_url); ?>" alt="Shop Cover" class="shop-cover-img">
            <div class="shop-cover-overlay"></div>
            <div class="shop-cover-content">
                <h1 class="shop-cover-title"><?php echo esc_html($page_title); ?></h1>
            </div>
        </div>

        
        <?php
        $beans_options   = function_exists('designer_coffee_get_shop_filter_terms') ? designer_coffee_get_shop_filter_terms('beans') : array();
        $process_options = function_exists('designer_coffee_get_shop_filter_terms') ? designer_coffee_get_shop_filter_terms('process') : array();
        $brew_options    = function_exists('designer_coffee_get_shop_filter_terms') ? designer_coffee_get_shop_filter_terms('brew') : array();

        if (empty($beans_options)) {
            $beans_options = array('espresso' => 'Espresso', 'filter' => 'Filtro / Filter', 'decaf' => 'Descafeinado / Decaf');
        }
        if (empty($process_options)) {
            $process_options = array('honey' => 'Honey', 'washed' => 'Lavado / Washed', 'natural' => 'Natural', 'semi-washed' => 'Semi-lavado');
        }
        if (empty($brew_options)) {
            $brew_options = array('espresso' => 'Espresso', 'drip' => 'Drip / Filter', 'pour-over' => 'Pour Over', 'french-press' => 'French Press');
        }
        ?>

        <!-- Top Filter Navigation Bar (Category Switcher + Clear Filters Button) -->
        <div class="shop-filter-top-bar">
            <div class="shop-category-switcher">
                <button type="button" class="shop-cat-btn active" data-cat-filter="beans" role="tab" aria-selected="true">
                    <span>Beans</span>
                </button>
                <button type="button" class="shop-cat-btn" data-cat-filter="merchandise" role="tab" aria-selected="false">
                    <span>Merchandise</span>
                </button>
            </div>
            <button type="button" class="shop-clear-filters-btn" id="shop-clear-filters" aria-label="Clear all applied filters">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.5 3.5L3.5 10.5M3.5 3.5L10.5 10.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Clear Filters</span>
            </button>
        </div>


        <!-- 3-Column Dropdown Filter System (Coffee Beans, Processing, Brew Method) - COLLAPSED BY DEFAULT -->
        <div class="shop-filter-system">
            <div class="shop-filter-grid">
                
                <!-- COLUMN 1: COFFEE BEANS -->
                <div class="shop-filter-col collapsed" data-filter-group="beans">
                    <div class="shop-filter-header" role="button" tabindex="0" aria-expanded="false">
                        <span class="filter-title">Coffee Beans</span>
                        <svg class="filter-chevron" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="shop-filter-options">
                        <label class="filter-radio-label active">
                            <input type="radio" name="filter_beans" value="all" checked>
                            <span class="radio-indicator"></span>
                            <span class="radio-text">All Beans</span>
                        </label>
                        <?php foreach ($beans_options as $slug => $label) : ?>
                            <label class="filter-radio-label">
                                <input type="radio" name="filter_beans" value="<?php echo esc_attr($slug); ?>">
                                <span class="radio-indicator"></span>
                                <span class="radio-text"><?php echo esc_html($label); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- COLUMN 2: PROCESSING -->
                <div class="shop-filter-col collapsed" data-filter-group="process">
                    <div class="shop-filter-header" role="button" tabindex="0" aria-expanded="false">
                        <span class="filter-title">Processing</span>
                        <svg class="filter-chevron" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="shop-filter-options">
                        <label class="filter-radio-label active">
                            <input type="radio" name="filter_process" value="all" checked>
                            <span class="radio-indicator"></span>
                            <span class="radio-text">All Processes</span>
                        </label>
                        <?php foreach ($process_options as $slug => $label) : ?>
                            <label class="filter-radio-label">
                                <input type="radio" name="filter_process" value="<?php echo esc_attr($slug); ?>">
                                <span class="radio-indicator"></span>
                                <span class="radio-text"><?php echo esc_html($label); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- COLUMN 3: BREW METHOD -->
                <div class="shop-filter-col collapsed" data-filter-group="brew">
                    <div class="shop-filter-header" role="button" tabindex="0" aria-expanded="false">
                        <span class="filter-title">Brew Method</span>
                        <svg class="filter-chevron" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="shop-filter-options">
                        <label class="filter-radio-label active">
                            <input type="radio" name="filter_brew" value="all" checked>
                            <span class="radio-indicator"></span>
                            <span class="radio-text">All Methods</span>
                        </label>
                        <?php foreach ($brew_options as $slug => $label) : ?>
                            <label class="filter-radio-label">
                                <input type="radio" name="filter_brew" value="<?php echo esc_attr($slug); ?>">
                                <span class="radio-indicator"></span>
                                <span class="radio-text"><?php echo esc_html($label); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>




        <!-- Product Grid Container -->
        <div class="shop-grid" id="shop-product-grid">
            <?php
            $shop_query = new WP_Query(array(
                'post_type'      => 'product',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
            ));

            if ($shop_query->have_posts()) :
                while ($shop_query->have_posts()) : $shop_query->the_post();
                    if (function_exists('designer_coffee_render_product_card')) {
                        designer_coffee_render_product_card();
                    }
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p class="no-products-msg">No products found.</p>';
            endif;
            ?>
        </div>

        <!-- Empty State Notice (JS Controlled) -->
        <div class="shop-empty-state" id="shop-empty-state" style="display: none;">
            <p class="empty-state-title">No products match this filter.</p>
            <button type="button" class="btn-reset-filter" id="btn-reset-filter">Show Beans</button>
        </div>

    </div>
</div>

<?php get_footer(); ?>
