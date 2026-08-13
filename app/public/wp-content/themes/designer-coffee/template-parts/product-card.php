<?php
/**
 * Atomic Product Card Component (template-parts/product-card.php)
 * Follows Atomic Design Architecture:
 *   - Organism: .shop-product-card container
 *   - Molecule: .product-image-box, .product-card-info
 *   - Atom: .sold-out-badge, .product-card-title, .product-price
 *
 * @package DesignerCoffee
 * @var array $args Passed arguments from designer_coffee_render_product_card() or get_template_part()
 */

if (!defined('ABSPATH')) {
    exit;
}

// Fallback logic if called directly without $args
if (empty($args) || !is_array($args)) {
    global $product;
    $product_id = get_the_ID();
    if ($product_id && function_exists('designer_coffee_render_product_card')) {
        designer_coffee_render_product_card($product_id);
        return;
    }
}

// Extract variables with clean fallbacks
$product_id  = isset($args['product_id']) ? intval($args['product_id']) : 0;
$title       = isset($args['title']) ? $args['title'] : get_the_title($product_id);
$permalink   = isset($args['permalink']) ? $args['permalink'] : get_permalink($product_id);
$price_html  = isset($args['price_html']) ? $args['price_html'] : '';
$image_html  = isset($args['image_html']) ? $args['image_html'] : '';
$is_sold_out = !empty($args['is_sold_out']);
$cat_attr    = isset($args['cat_attr']) ? $args['cat_attr'] : 'all';
$beans_attr  = isset($args['beans_attr']) ? $args['beans_attr'] : 'all';
$proc_attr   = isset($args['proc_attr']) ? $args['proc_attr'] : 'all';
$brew_attr   = isset($args['brew_attr']) ? $args['brew_attr'] : 'all';
$extra_class = isset($args['extra_class']) ? ' ' . trim($args['extra_class']) : '';

// Default image fallback if missing
if (empty($image_html)) {
    if ($product_id > 0 && has_post_thumbnail($product_id)) {
        $image_html = get_the_post_thumbnail($product_id, 'large', array('class' => 'product-img', 'alt' => esc_attr($title)));
    } else {
        $image_html = '<img src="' . esc_url(home_url('/wp-content/uploads/2026/02/250GR_PRODUCT_HONEYQ.png')) . '" alt="' . esc_attr($title) . '" class="product-img">';
    }
}
?>

<!-- Organism: Product Card Component -->
<div class="shop-product-card<?php echo $is_sold_out ? ' is-sold-out' : ''; ?><?php echo esc_attr($extra_class); ?>" 
     data-product-id="<?php echo esc_attr($product_id); ?>" 
     data-categories="<?php echo esc_attr($cat_attr); ?>" 
     data-beans="<?php echo esc_attr($beans_attr); ?>" 
     data-process="<?php echo esc_attr($proc_attr); ?>" 
     data-brew="<?php echo esc_attr($brew_attr); ?>" 
     data-title="<?php echo esc_attr(strtolower($title)); ?>">

    <!-- Molecule: Product Image Box -->
    <a href="<?php echo esc_url($permalink); ?>" class="product-image-box" aria-label="<?php echo esc_attr($title); ?>">
        <?php echo $image_html; ?>
        <?php if ($is_sold_out) : ?>
            <!-- Atom: Sold Out Badge -->
            <span class="sold-out-badge">Sold Out</span>
        <?php endif; ?>
    </a>

    <!-- Molecule: Product Info Summary -->
    <div class="product-card-info">
        <!-- Atom: Title Link -->
        <h3 class="product-card-title">
            <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
        </h3>
        <!-- Atom: Price Display -->
        <div class="product-price-wrapper">
            <span class="product-price"><?php echo $price_html; ?></span>
        </div>
    </div>

</div>

