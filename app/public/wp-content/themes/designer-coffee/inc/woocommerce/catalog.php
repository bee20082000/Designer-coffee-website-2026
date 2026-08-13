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
   9. REUSABLE PRODUCT CARD COMPONENT
   ========================================================================== */
function designer_coffee_render_product_card($product_data = null, $args = array()) {
    $product = null;
    $product = null;
    $product_id = 0;
    $title = '';
    $permalink = '#';
    $price_html = '';
    $image_html = '';
    $is_sold_out = false;

    if (is_a($product_data, 'WC_Product')) {
        $product = $product_data;
    } elseif (is_numeric($product_data)) {
        $product = function_exists('wc_get_product') ? wc_get_product($product_data) : null;
    } elseif (empty($product_data)) {
        global $product;
        if (!$product && function_exists('wc_get_product')) {
            $product = wc_get_product(get_the_ID());
        }
    }

    if ($product && is_a($product, 'WC_Product')) {
        $product_id  = $product->get_id();
        $permalink   = get_permalink($product_id);
        $title       = $product->get_name();
        if ($product->is_type('variable')) {
            $min_price = $product->get_variation_price('min', true);
            $price_html = '<span class="price-from">From </span>' . wc_price($min_price);
        } else {
            $price_html = $product->get_price_html();
        }


        $is_sold_out = !$product->is_in_stock();
        if (has_post_thumbnail($product_id)) {
            $image_html = get_the_post_thumbnail($product_id, 'large', array('class' => 'product-img', 'alt' => esc_attr($title)));
        } else {
            $image_html = '<img src="' . esc_url(home_url('/wp-content/uploads/2026/02/250GR_PRODUCT_HONEYQ.png')) . '" alt="' . esc_attr($title) . '" class="product-img">';
        }
    } elseif (is_array($product_data)) {
        $product_id  = isset($product_data['id']) ? intval($product_data['id']) : 0;
        $title       = isset($product_data['title']) ? $product_data['title'] : 'Coffee Product';
        $permalink   = isset($product_data['permalink']) ? $product_data['permalink'] : (class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/shop'));
        $price_html  = isset($product_data['price']) ? $product_data['price'] : '';
        $is_sold_out = !empty($product_data['is_sold_out']);
        $img_src     = isset($product_data['image']) ? $product_data['image'] : home_url('/wp-content/uploads/2026/02/250GR_PRODUCT_HONEYQ.png');
        $image_html  = '<img src="' . esc_url($img_src) . '" alt="' . esc_attr($title) . '" class="product-img">';
    } else {
        return;
    }

    $categories_list = array('all');
    $beans_list      = array('all');
    $proc_list       = array('all');
    $brew_list       = array('all');

    // WooCommerce Attributes extraction (Bean Type, Processing, Brew Method)
    if (!$product && $product_id > 0 && function_exists('wc_get_product')) {
        $product = wc_get_product($product_id);
    }

    if ($product && is_a($product, 'WC_Product')) {
        $wc_attributes = $product->get_attributes();
        if (!empty($wc_attributes)) {
            foreach ($wc_attributes as $attr_key => $attr) {
                $label = strtolower(wc_attribute_label($attr->get_name()));
                $name  = strtolower($attr->get_name());
                $vals  = array();

                if ($attr->is_taxonomy()) {
                    $terms = wc_get_product_terms($product_id, $attr->get_name(), array('fields' => 'all'));
                    if (!is_wp_error($terms) && !empty($terms)) {
                        foreach ($terms as $t) {
                            $vals[] = strtolower($t->slug);
                            $vals[] = strtolower($t->name);
                        }
                    }
                } else {
                    $opts = $attr->get_options();
                    if (!empty($opts)) {
                        foreach ($opts as $opt) {
                            $vals[] = strtolower(sanitize_title($opt));
                            $vals[] = strtolower($opt);
                        }
                    }
                }

                if (!empty($vals)) {
                    if (strpos($label, 'bean') !== false || strpos($label, 'type') !== false || strpos($name, 'bean') !== false || strpos($name, 'type') !== false) {
                        $beans_list = array_merge($beans_list, $vals);
                    }
                    if (strpos($label, 'process') !== false || strpos($label, 'proceso') !== false || strpos($label, 'processing') !== false || strpos($name, 'process') !== false || strpos($name, 'proceso') !== false) {
                        $proc_list = array_merge($proc_list, $vals);
                    }
                    if (strpos($label, 'brew') !== false || strpos($label, 'method') !== false || strpos($label, 'método') !== false || strpos($name, 'brew') !== false || strpos($name, 'method') !== false) {
                        $brew_list = array_merge($brew_list, $vals);
                    }
                }
            }
        }
    }

    if ($product_id > 0) {
        $terms = get_the_terms($product_id, 'product_cat');
        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $t) {
                $slug = strtolower($t->slug);
                $name = strtolower($t->name);
                $categories_list[] = $slug;
                $categories_list[] = $name;

                if (strpos($slug, 'espresso') !== false || strpos($name, 'espresso') !== false) { $beans_list[] = 'espresso'; $brew_list[] = 'espresso'; }
                if (strpos($slug, 'filter') !== false || strpos($name, 'filter') !== false || strpos($slug, 'filtro') !== false) { $beans_list[] = 'filter'; $brew_list[] = 'drip'; }
                if (strpos($slug, 'decaf') !== false || strpos($name, 'decaf') !== false || strpos($slug, 'descafeinado') !== false) { $beans_list[] = 'decaf'; }
            }
        }

        $tags = get_the_terms($product_id, 'product_tag');
        if (!empty($tags) && !is_wp_error($tags)) {
            foreach ($tags as $tag) {
                $slug = strtolower($tag->slug);
                $categories_list[] = $slug;
                if (strpos($slug, 'honey') !== false) $proc_list[] = 'honey';
                if (strpos($slug, 'wash') !== false || strpos($slug, 'lavado') !== false) $proc_list[] = 'washed';
                if (strpos($slug, 'natural') !== false) $proc_list[] = 'natural';
                if (strpos($slug, 'semi') !== false) $proc_list[] = 'semi-washed';

                if (strpos($slug, 'drip') !== false || strpos($slug, 'filter') !== false) $brew_list[] = 'drip';
                if (strpos($slug, 'espresso') !== false) $brew_list[] = 'espresso';
                if (strpos($slug, 'pour') !== false) $brew_list[] = 'pour-over';
                if (strpos($slug, 'french') !== false) $brew_list[] = 'french-press';
            }
        }

        $title_lower = strtolower($title);
        if (strpos($title_lower, 'espresso') !== false) { $beans_list[] = 'espresso'; $brew_list[] = 'espresso'; }
        if (strpos($title_lower, 'filter') !== false || strpos($title_lower, 'filtro') !== false) { $beans_list[] = 'filter'; $brew_list[] = 'drip'; }
        if (strpos($title_lower, 'decaf') !== false || strpos($title_lower, 'descafeinado') !== false) { $beans_list[] = 'decaf'; }
        if (strpos($title_lower, 'honey') !== false) { $proc_list[] = 'honey'; }
        if (strpos($title_lower, 'washed') !== false || strpos($title_lower, 'lavado') !== false) { $proc_list[] = 'washed'; }
        if (strpos($title_lower, 'natural') !== false) { $proc_list[] = 'natural'; }
        if (strpos($title_lower, 'semi-washed') !== false || strpos($title_lower, 'semi-lavado') !== false) { $proc_list[] = 'semi-washed'; }
    }

    $card_args = array(
        'product_id'  => $product_id,
        'title'       => $title,
        'permalink'   => $permalink,
        'price_html'  => $price_html,
        'image_html'  => $image_html,
        'is_sold_out' => $is_sold_out,
        'cat_attr'    => implode(' ', array_unique((array)$categories_list)),
        'beans_attr'  => implode(' ', array_unique((array)$beans_list)),
        'proc_attr'   => implode(' ', array_unique((array)$proc_list)),
        'brew_attr'   => implode(' ', array_unique((array)$brew_list)),
        'extra_class' => isset($args['class']) ? esc_attr($args['class']) : '',
    );

    get_template_part('template-parts/product-card', null, $card_args);
}
/* ==========================================================================
   DYNAMIC WOOCOMMERCE SHOP FILTER OPTIONS QUERY (VIA WC ATTRIBUTES & TAXONOMIES)
   ========================================================================== */
function designer_coffee_get_shop_filter_terms($type = 'beans') {
    $options = array();

    if (!class_exists('WooCommerce')) {
        return $options;
    }

    // 1. Query WooCommerce Global Attribute Taxonomies via wc_get_attribute_taxonomies()
    $wc_attributes = function_exists('wc_get_attribute_taxonomies') ? wc_get_attribute_taxonomies() : array();
    $target_taxonomies = array();

    if (!empty($wc_attributes)) {
        foreach ($wc_attributes as $attr) {
            $tax_name   = wc_attribute_taxonomy_name($attr->attribute_name);
            $name_lower = strtolower($attr->attribute_name);
            $label_lower = strtolower($attr->attribute_label);

            if ($type === 'beans' && (strpos($name_lower, 'bean') !== false || strpos($name_lower, 'type') !== false || strpos($label_lower, 'bean') !== false || strpos($label_lower, 'category') !== false)) {
                $target_taxonomies[] = $tax_name;
            } elseif ($type === 'process' && (strpos($name_lower, 'process') !== false || strpos($name_lower, 'proceso') !== false || strpos($label_lower, 'process') !== false || strpos($label_lower, 'proceso') !== false)) {
                $target_taxonomies[] = $tax_name;
            } elseif ($type === 'brew' && (strpos($name_lower, 'brew') !== false || strpos($name_lower, 'method') !== false || strpos($name_lower, 'metodo') !== false || strpos($label_lower, 'brew') !== false || strpos($label_lower, 'method') !== false)) {
                $target_taxonomies[] = $tax_name;
            }
        }
    }

    // Default taxonomy fallbacks if not matched by label
    if (empty($target_taxonomies)) {
        if ($type === 'beans') {
            $target_taxonomies = array('pa_bean', 'pa_beans', 'pa_coffee_type', 'pa_type');
        } elseif ($type === 'process') {
            $target_taxonomies = array('pa_process', 'pa_processing', 'pa_proceso');
        } elseif ($type === 'brew') {
            $target_taxonomies = array('pa_brew_method', 'pa_brew', 'pa_method', 'pa_metodo');
        }
    }

    // Query terms for identified taxonomies
    if (!empty($target_taxonomies)) {
        $terms = get_terms(array(
            'taxonomy'   => $target_taxonomies,
            'hide_empty' => false,
        ));
        if (!is_wp_error($terms) && !empty($terms)) {
            foreach ($terms as $term) {
                $options[strtolower($term->slug)] = $term->name;
            }
        }
    }

    // For 'beans', also include product_cat categories (excluding generic coffee/merchandise)
    if ($type === 'beans') {
        $cats = get_terms(array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
        ));
        if (!is_wp_error($cats) && !empty($cats)) {
            foreach ($cats as $cat) {
                $slug = strtolower($cat->slug);
                if (in_array($slug, array('uncategorized', 'coffee', 'merchandise', 'merch', 'apparel', 'gear', 'accessories', 't-shirts', 'mugs'))) continue;
                $options[$slug] = $cat->name;
            }
        }
    }

    // Fallback: scan product attributes when no global terms are configured.
    if (empty($options)) {
        $products = wc_get_products(array('limit' => 50, 'status' => 'publish'));
        foreach ($products as $product) {
            foreach ($product->get_attributes() as $attr) {
                $label = wc_attribute_label($attr->get_name());
                $is_target = false;

                if ($type === 'beans' && (stripos($label, 'bean') !== false || stripos($label, 'category') !== false)) {
                    $is_target = true;
                } elseif ($type === 'process' && (stripos($label, 'process') !== false || stripos($label, 'proceso') !== false)) {
                    $is_target = true;
                } elseif ($type === 'brew' && (stripos($label, 'brew') !== false || stripos($label, 'method') !== false || stripos($label, 'método') !== false)) {
                    $is_target = true;
                }

                if (!$is_target) {
                    continue;
                }

                if ($attr->is_taxonomy()) {
                    $terms = wc_get_product_terms($product->get_id(), $attr->get_name(), array('fields' => 'all'));
                    if (!is_wp_error($terms)) {
                        foreach ($terms as $term) {
                            $options[strtolower($term->slug)] = $term->name;
                        }
                    }
                } else {
                    foreach ($attr->get_options() as $option) {
                        $options[strtolower(sanitize_title($option))] = $option;
                    }
                }
            }
        }
    }

    return $options;
}

