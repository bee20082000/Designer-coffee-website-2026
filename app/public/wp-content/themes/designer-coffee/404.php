<?php
/**
 * 404 Error Page Template
 *
 * @package DesignerCoffee
 */

get_header();
?>

<section class="section" style="padding-top: 11rem; padding-bottom: 8rem; text-align: center;">
    <div class="container" style="max-width: 650px;">
        <span class="hero-badge">404 ERROR</span>
        <h1 style="font-size: 5rem; color: var(--accent-gold); margin-bottom: 0.5rem;">404</h1>
        <h2 style="margin-bottom: 1.5rem;">Page Not Found</h2>
        <p style="color: var(--text-muted); margin-bottom: 2.5rem;">
            The page or brew you are looking for has moved or no longer exists. Try searching or head back to the main homepage.
        </p>

        <div style="margin-bottom: 2.5rem;">
            <?php get_search_form(); ?>
        </div>

        <div class="btn-group">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">Back to Homepage</a>
            <?php if (class_exists('WooCommerce')) : ?>
                <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="btn btn-outline">Visit Shop</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
