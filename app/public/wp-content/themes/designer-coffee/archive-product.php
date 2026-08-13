<?php
/**
 * The Template for displaying product archives, including the main shop page
 * Location: archive-product.php
 *
 * @package DesignerCoffee
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="shop-page-wrapper section">
    <div class="container">
        <div class="shop-section-header">
            <h1 class="shop-title-main">
                <?php
                if (is_product_category()) {
                    single_term_title();
                } else {
                    echo 'Shop collection';
                }
                ?>
            </h1>
        </div>

        <div class="shop-grid">
            <?php
            if (have_posts()) :
                while (have_posts()) : the_post();
                    get_template_part('template-parts/product-card');
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p style="text-align:center; grid-column: 1/-1; padding: 4rem 0;">No products found.</p>';
            endif;
            ?>
        </div>
    </div>
</div>

<?php
get_footer();
