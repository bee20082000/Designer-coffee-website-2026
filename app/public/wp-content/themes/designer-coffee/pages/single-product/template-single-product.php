<?php
/**
 * Template Name: Single Product Page Template
 * Location: pages/single-product/template-single-product.php
 *
 * Modern Coffee Product Template matching custom horizontal 4-column specification layout:
 *  - Dynamic price updating based on selected size pill
 *  - Clean WooCommerce Info & Attributes layout
 *  - 4-column spec grid matching design reference
 *
 * @package DesignerCoffee
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

global $product;
if (!$product || !is_a($product, 'WC_Product')) {
    $product = wc_get_product(get_the_ID());
}

if ($product) :
    $product_id        = $product->get_id();
    $title             = $product->get_name();
    $price_html        = $product->get_price_html();
    $raw_price         = (float) $product->get_price();
    $currency_symbol   = function_exists('get_woocommerce_currency_symbol') ? get_woocommerce_currency_symbol() : '$';
    $is_in_stock       = $product->is_in_stock();
    $short_desc        = $product->get_short_description();
    $full_content      = get_the_content();
    $categories        = wc_get_product_category_list($product_id, ', ');

    // Variation JSON data for variable products
    $variations_json   = $product->is_type('variable') ? wp_json_encode($product->get_available_variations()) : '[]';

    // Main image & Gallery images
    $main_img_id       = $product->get_image_id();
    $main_img_src      = $main_img_id ? wp_get_attachment_image_url($main_img_id, 'full') : home_url('/wp-content/uploads/2026/02/250GR_PRODUCT_HONEYQ.png');
    $gallery_img_ids   = $product->get_gallery_image_ids();
    if (!empty($main_img_id) && !in_array($main_img_id, $gallery_img_ids)) {
        array_unshift($gallery_img_ids, $main_img_id);
    }

    /* --------------------------------------------------------------------------
       EXTRACT ACF FIELDS (for Specs below price)
       -------------------------------------------------------------------------- */
    $acf_fields = array();

    // 1. Check ACF Group
    $acf_group = function_exists('get_field') ? get_field('coffee_details', $product_id) : null;
    if (!is_array($acf_group) && function_exists('get_field')) {
        $acf_group = get_field('coffee-details', $product_id);
    }

    if (is_array($acf_group)) {
        foreach ($acf_group as $k => $v) {
            if (!empty($v) && !is_array($v)) {
                $label = ucwords(str_replace(array('_', '-'), ' ', $k));
                $acf_fields[$label] = $v;
            }
        }
    }

    // 2. Check standalone ACF fields
    if (function_exists('get_field_objects')) {
        $field_objs = get_field_objects($product_id);
        if (!empty($field_objs) && is_array($field_objs)) {
            foreach ($field_objs as $k => $obj) {
                if (!empty($obj['value']) && !is_array($obj['value'])) {
                    $label = !empty($obj['label']) ? $obj['label'] : ucwords(str_replace(array('_', '-'), ' ', $obj['name']));
                    $acf_fields[$label] = $obj['value'];
                }
            }
        }
    }

    // Fallbacks
    $known_keys = array(
        'Process'       => array('process', 'coffee_process', 'processing_method'),
        'Tasting Notes' => array('flavor_notes', 'tasting_notes', 'coffee_flavor_notes', 'cupping_notes'),
        'Brew Method'   => array('brew_recommendation', 'brewing_guide', 'brew_guide', 'brew_method'),
        'Origin'        => array('origin', 'coffee_origin', 'country_origin'),
        'Variety'       => array('variety', 'coffee_variety', 'cultivar'),
        'Roast Level'   => array('roast_level', 'roast', 'coffee_roast_level'),
        'Producer'      => array('producer', 'coffee_producer', 'farm'),
    );

    foreach ($known_keys as $label => $keys) {
        if (!isset($acf_fields[$label]) && function_exists('get_field')) {
            foreach ($keys as $key) {
                $val = get_field($key, $product_id);
                if ($val !== null && $val !== false && $val !== '') {
                    $acf_fields[$label] = $val;
                    break;
                }
            }
        }
    }

    /* --------------------------------------------------------------------------
       EXTRACT WOOCOMMERCE ATTRIBUTES (for Coffee Details section)
       -------------------------------------------------------------------------- */
     $wc_attribute_specs = array();
     $wc_attributes = $product->get_attributes();
     if (!empty($wc_attributes)) {
         foreach ($wc_attributes as $attr_key => $attribute) {
             $label = wc_attribute_label($attribute->get_name());
             if (stripos($label, 'size') !== false || stripos($label, 'elevation') !== false || stripos($label, 'altitude') !== false) {
                 continue;
             }
             $values = array();

            if ($attribute->is_taxonomy()) {
                $terms = wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'names'));
                if (!is_wp_error($terms) && !empty($terms)) {
                    $values = $terms;
                }
            } else {
                $options = $attribute->get_options();
                if (!empty($options)) {
                    $values = $options;
                }
            }

            if (!empty($values)) {
                $wc_attribute_specs[$label] = implode(', ', $values);
            }
        }
    }

    // Remove any leftover Elevation fields
    if (isset($wc_attribute_specs['Elevation'])) unset($wc_attribute_specs['Elevation']);
    if (isset($wc_attribute_specs['elevation'])) unset($wc_attribute_specs['elevation']);
    if (isset($wc_attribute_specs['Altitude'])) unset($wc_attribute_specs['Altitude']);
    if (isset($wc_attribute_specs['altitude'])) unset($wc_attribute_specs['altitude']);

    // Fallback specification specs if missing
    if (empty($wc_attribute_specs)) {
        $wc_attribute_specs = array(
            'Process'       => 'Washed / Natural',
            'Tasting Notes' => 'Milk Chocolate, Blackberry, Caramel',
            'Brew Method'   => 'Drip, Espresso',
            'Roast Level'   => 'Medium Roast',
        );
    }




    // Size / Weight Attribute Options
    $size_options_list = array();
    if ($product->is_type('variable')) {
        $attributes = $product->get_variation_attributes();
        if (!empty($attributes)) {
            foreach ($attributes as $attr_name => $options) {
                if (!empty($options)) {
                    $size_options_list[wc_attribute_label($attr_name)] = $options;
                }
            }
        }
    } elseif (!empty($wc_attributes)) {
        foreach ($wc_attributes as $attr_name => $attribute) {
            $label = wc_attribute_label($attribute->get_name());
            if (stripos($label, 'size') !== false || stripos($label, 'weight') !== false || stripos($label, 'peso') !== false || stripos($label, 'bag') !== false) {
                $options = $attribute->is_taxonomy() ? wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'names')) : $attribute->get_options();
                if (!empty($options)) {
                    $size_options_list[$label] = $options;
                }
            }
        }
    }

    if (empty($size_options_list)) {
        $size_options_list['Weight'] = array('250g', '500g', '1kg');
    }

    // Main featured product photo (ALWAYS FRONT ITEM at index 0)
    $main_img_id  = $product->get_image_id();
    $main_img_src = $main_img_id ? wp_get_attachment_image_url($main_img_id, 'full') : home_url('/wp-content/uploads/2026/02/250GR_PRODUCT_HONEYQ.png');
    $gallery_img_ids = $product->get_gallery_image_ids();

    $all_gallery_items = array(
        array(
            'type' => 'image',
            'url'  => $main_img_src,
        )
    );

    // Extract Coffee WooCommerce Mixed Gallery Plugin items (_coffee_mixed_gallery)
    $plugin_gallery = get_post_meta($product_id, '_coffee_mixed_gallery', true);

    if (!empty($plugin_gallery) && is_array($plugin_gallery)) {
        foreach ($plugin_gallery as $item) {
            if (isset($item['type']) && 'image' === $item['type'] && !empty($item['id'])) {
                $full = wp_get_attachment_image_url((int)$item['id'], 'full');
                if ($full && $full !== $main_img_src) {
                    $all_gallery_items[] = array(
                        'type' => 'image',
                        'url'  => $full,
                    );
                }
            } elseif (isset($item['type']) && 'youtube' === $item['type'] && !empty($item['url'])) {
                $v_id = function_exists('coffee_wcg_youtube_id') ? coffee_wcg_youtube_id($item['url']) : '';
                if (!$v_id && preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $item['url'], $m)) {
                    $v_id = $m[1];
                }
                if ($v_id) {
                    $all_gallery_items[] = array(
                        'type'  => 'youtube',
                        'url'   => 'https://www.youtube.com/embed/' . rawurlencode($v_id) . '?autoplay=1&mute=1&enablejsapi=1',
                        'thumb' => 'https://img.youtube.com/vi/' . rawurlencode($v_id) . '/hqdefault.jpg',
                    );
                }
            }
        }
    }

    // Add remaining native WooCommerce gallery images if not already included
    if (!empty($gallery_img_ids)) {
        foreach ($gallery_img_ids as $g_id) {
            $g_src = wp_get_attachment_image_url($g_id, 'full');
            if ($g_src && $g_src !== $main_img_src && !in_array($g_src, array_column($all_gallery_items, 'url'))) {
                $all_gallery_items[] = array(
                    'type' => 'image',
                    'url'  => $g_src,
                );
            }
        }
    }

    $first_item = reset($all_gallery_items);
    $main_img_src = ($first_item && isset($first_item['url'])) ? $first_item['url'] : home_url('/wp-content/uploads/2026/02/250GR_PRODUCT_HONEYQ.png');
    $first_type   = ($first_item && isset($first_item['type'])) ? $first_item['type'] : 'image';
?>

<div class="nomad-single-product-wrapper">
    
    <!-- LEFT COLUMN: Full-Height Image/Video Gallery Slider with Navigation Arrows -->
    <div class="sp-left-gallery-col" id="sp-gallery-slider" data-gallery-items="<?php echo esc_attr(wp_json_encode($all_gallery_items)); ?>">
        <div class="sp-gallery-main-box">
            <?php if ('youtube' === $first_type) : ?>
                <iframe id="sp-main-frame" src="<?php echo esc_url($main_img_src); ?>" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen title="Product video"></iframe>
            <?php else : ?>
                <img id="sp-main-img" src="<?php echo esc_url($main_img_src); ?>" alt="<?php echo esc_attr($title); ?>">
            <?php endif; ?>

            <?php if (count($all_gallery_items) > 1) : ?>
                <!-- Interactive Navigation Arrows -->
                <button type="button" class="sp-gallery-arrow sp-gallery-prev" aria-label="Previous item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <button type="button" class="sp-gallery-arrow sp-gallery-next" aria-label="Next item">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>

                <!-- Item Dots Indicator Bar -->
                <div class="sp-gallery-dots">
                    <?php foreach ($all_gallery_items as $idx => $g_item) : ?>
                        <button type="button" class="sp-gallery-dot<?php echo $idx === 0 ? ' active' : ''; ?><?php echo 'youtube' === $g_item['type'] ? ' is-video' : ''; ?>" data-index="<?php echo $idx; ?>" aria-label="Go to item <?php echo $idx + 1; ?>">
                            <?php if ('youtube' === $g_item['type']) : ?>
                                <span class="dot-play-icon">▶</span>
                            <?php endif; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>



    <!-- RIGHT COLUMN: Sticky Scrolling Info & Control Panel -->
    <div class="sp-right-info-col" 
         data-raw-price="<?php echo esc_attr($raw_price); ?>" 
         data-currency-symbol="<?php echo esc_attr($currency_symbol); ?>"
         data-variations="<?php echo esc_attr($variations_json); ?>">
         
        <div class="sp-info-inner">
            
            <!-- Single Line Uppercase Title Header -->
            <h1 class="sp-title-header"><?php echo esc_html(strtoupper($title)); ?></h1>


            <!-- Description Text (Fragrance, Aroma, Notes & Body) -->
            <div class="sp-description-text">
                <?php 
                if (!empty($short_desc)) {
                    echo wp_kses_post($short_desc);
                } elseif (!empty($full_content)) {
                    echo wp_kses_post(wp_trim_words($full_content, 90));
                } else {
                    echo '<p>En fragancia y aroma es un café muy dulce con notas equilibradas. En boca se distinguen matices limpios y fruta fresca, con acidez suave y un retrogusto muy agradable.</p>';
                }
                ?>
            </div>

            <!-- Specs & Select Option Rows -->
            <div class="sp-option-rows-group">
                
                <!-- Dynamic Weight / Size Dropdown Row -->
                <?php foreach ($size_options_list as $opt_title => $opt_values) : 
                    if (empty($opt_values) || !is_array($opt_values)) continue;
                ?>
                    <div class="sp-option-row" data-attribute="<?php echo esc_attr(strtolower($opt_title)); ?>">
                        <span class="sp-row-label"><?php echo esc_html($opt_title); ?>:</span>
                        <div class="sp-row-select-wrapper">
                            <select class="sp-row-select sp-size-select" aria-label="Select <?php echo esc_attr($opt_title); ?>">
                                <?php foreach ($opt_values as $idx => $val) : ?>
                                    <option value="<?php echo esc_attr($val); ?>" <?php echo $idx === 0 ? 'selected' : ''; ?>><?php echo esc_html($val); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <svg class="sp-select-chevron" width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Grind Size Options Row: "Grind" -->
                <?php 
                $grind_options = array();
                $grind_label   = 'Grind';


                // 1. Look for explicit attributes on the current product
                if ($product && is_a($product, 'WC_Product')) {
                    $wc_attributes = $product->get_attributes();
                    if (!empty($wc_attributes)) {
                        foreach ($wc_attributes as $attr_name => $attribute) {
                            $label = wc_attribute_label($attribute->get_name());
                            if (stripos($label, 'prepared') !== false || stripos($label, 'how would') !== false || stripos($label, 'grind') !== false || stripos($label, 'brew') !== false || stripos($label, 'phin') !== false) {
                                $opts = $attribute->is_taxonomy() ? wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'names')) : $attribute->get_options();
                                if (!empty($opts) && !is_wp_error($opts)) {
                                    $grind_options = $opts;
                                    break;
                                }
                            }
                        }
                    }
                }

                // 2. Query registered WooCommerce attribute taxonomies from database
                if (empty($grind_options) && function_exists('wc_get_attribute_taxonomies')) {
                    $attribute_taxonomies = wc_get_attribute_taxonomies();
                    if (!empty($attribute_taxonomies)) {
                        foreach ($attribute_taxonomies as $tax) {
                            $taxonomy_name = wc_attribute_taxonomy_name($tax->attribute_name);
                            if (taxonomy_exists($taxonomy_name)) {
                                $terms = get_terms(array(
                                    'taxonomy'   => $taxonomy_name,
                                    'hide_empty' => false,
                                    'fields'     => 'names',
                                ));
                                if (!empty($terms) && !is_wp_error($terms)) {
                                    foreach ($terms as $t_name) {
                                        if (in_array(strtolower($t_name), array('cold brew', 'espresso', 'phin', 'pour over', 'whole bean'))) {
                                            $grind_options = $terms;
                                            break;
                                        }
                                    }
                                    if (!empty($grind_options)) break;
                                }
                            }
                        }
                    }
                }

                // 3. Fallback to exact WooCommerce terms defined in Admin: Cold Brew, Espresso, Phin, Pour Over, Whole Bean
                if (empty($grind_options)) {
                    $grind_options = array(
                        'Cold Brew',
                        'Espresso',
                        'Phin',
                        'Pour Over',
                        'Whole Bean'
                    );
                }
                ?>
                <!-- Coffee Grind Selector Plugin Options Row (coffee-grind-selector) -->
                <?php 
                $cgs_options = function_exists('cgs_get_grind_options') ? cgs_get_grind_options() : array();
                $cgs_settings = function_exists('cgs_get_options') ? cgs_get_options() : array();
                $grind_label = !empty($cgs_settings['field_label']) ? $cgs_settings['field_label'] : 'How would you like your coffee ground?';
                
                $rec_value = '';
                if (function_exists('cgs_get_recommended_grind')) {
                    $rec_value = sanitize_key(cgs_get_recommended_grind($product_id));
                }

                if (empty($cgs_options)) {
                    $cgs_options = array(
                        'whole-bean'  => array('label' => 'Whole Bean', 'description' => "I'll grind it myself"),
                        'fine'        => array('label' => 'Fine', 'description' => 'Espresso'),
                        'medium-fine' => array('label' => 'Medium-Fine', 'description' => 'Phin · AeroPress'),
                        'medium'      => array('label' => 'Medium', 'description' => 'Pour Over · Drip'),
                        'coarse'      => array('label' => 'Coarse', 'description' => 'French Press · Cold Brew'),
                    );
                }
                ?>
                <div class="sp-option-row sp-cgs-grind-row" data-attribute="grind">
                    <span class="sp-row-label"><?php echo esc_html($grind_label); ?>:</span>
                    <div class="sp-grind-radio-group">
                        <?php 
                        $first_key = array_key_first($cgs_options);
                        foreach ($cgs_options as $val_key => $opt) : 
                            $is_rec = ($rec_value && $rec_value === $val_key);
                            $is_checked = $is_rec || ($val_key === $first_key);
                        ?>
                            <label class="sp-grind-radio-item<?php echo $is_checked ? ' is-active' : ''; ?>">
                                <input type="radio" name="cgs_grind" value="<?php echo esc_attr($val_key); ?>" <?php echo $is_checked ? 'checked' : ''; ?>>
                                <span class="sp-radio-content">
                                    <span class="sp-radio-title"><?php echo esc_html($opt['label']); ?></span>
                                    <?php if (!empty($opt['description'])) : ?>
                                        <span class="sp-radio-desc">— <?php echo esc_html($opt['description']); ?></span>
                                    <?php endif; ?>
                                </span>
                                <?php if ($is_rec) : ?>
                                    <span class="sp-radio-badge">Recommended</span>
                                <?php endif; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>







                <!-- Price Row -->
                <div class="sp-option-row sp-price-row">
                    <span class="sp-row-label">Price:</span>
                    <div class="sp-dynamic-price-value" id="sp-dynamic-price">
                        <span class="price-from">From </span><?php echo $price_html; ?>
                    </div>
                </div>

            </div>

            <!-- Action Controls Row: Capsule Quantity Selector + Black Pill Add to Cart Button -->
            <div class="sp-action-controls-row">
                <?php if ($is_in_stock) : ?>
                    <div class="sp-qty-capsule">
                        <button type="button" class="sp-qty-btn sp-qty-minus" aria-label="Decrease quantity">−</button>
                        <input type="number" class="sp-qty-input" value="1" min="1" max="99">
                        <button type="button" class="sp-qty-btn sp-qty-plus" aria-label="Increase quantity">+</button>
                    </div>

                    <button type="button" class="sp-add-to-cart-pill-btn ajax-add-to-cart-btn" data-product-id="<?php echo esc_attr($product_id); ?>">
                        <span>Add to Cart</span>
                    </button>
                <?php else : ?>
                    <button type="button" class="sp-add-to-cart-pill-btn disabled" disabled>
                        <span>Out of Stock</span>
                    </button>
                <?php endif; ?>
            </div>

            <!-- Hidden WooCommerce Form for Variable Products processing -->
            <?php if ($product->is_type('variable')) : ?>
                <div class="sp-variable-form-wrapper" style="display: none !important;">
                    <?php woocommerce_variable_add_to_cart(); ?>
                </div>
            <?php endif; ?>

            <!-- Additional Specifications List Block -->
            <?php if (!empty($wc_attribute_specs)) : ?>
                <div class="sp-extra-details-block">
                    <h3 class="sp-extra-title">Coffee Details</h3>
                    <div class="sp-specs-list">
                        <?php foreach ($wc_attribute_specs as $label => $val) : 
                            if (empty($val)) continue;
                            $val_str = is_array($val) ? implode(', ', $val) : $val;
                        ?>
                            <div class="sp-spec-item">
                                <span class="sp-spec-key"><?php echo esc_html($label); ?>:</span>
                                <span class="sp-spec-val"><?php echo esc_html($val_str); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Full-Width Recommended Products Grid (Outside Split Screen Hero) -->
<?php
$related_ids = function_exists('wc_get_related_products') ? wc_get_related_products($product_id, 4) : array();
if (!empty($related_ids)) :
?>
    <div class="sp-related-section-wrapper section">
        <div class="container">
            <h2 class="sp-section-heading">You May Also Like</h2>
            <div class="sp-related-grid">
                <?php
                foreach ($related_ids as $rel_id) {
                    if (function_exists('designer_coffee_render_product_card')) {
                        designer_coffee_render_product_card($rel_id);
                    }
                }
                ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php 
endif; // $product

get_footer(); 
?>


