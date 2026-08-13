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
   3. ACF HELPER — get_field() with safe fallback
   ========================================================================== */
function designer_get_field($field_name, $post_id = false, $default = '') {
    if (function_exists('get_field')) {
        $val = get_field($field_name, $post_id);
        if ($val !== null && $val !== false && $val !== '') {
            return $val;
        }
    }
    return $default;
}

/* ==========================================================================
   4. ACF FIELD GROUPS — registered in code, appear in page editor when ACF
      plugin is active. Site works with hardcoded defaults if ACF is absent.
   ========================================================================== */
add_action('acf/init', 'designer_coffee_register_acf_fields');

function designer_coffee_register_acf_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    // --- Homepage hero slides (Front Page) ---
    acf_add_local_field_group(array(
        'key'    => 'group_dc_homepage',
        'title'  => '🏠 Homepage Content',
        'fields' => array(
            array('key' => 'field_dc_hero0_tab',          'label' => 'Hero Slide 1',           'name' => '',                  'type' => 'tab'),
            array('key' => 'field_dc_hero_slide0_title',  'label' => 'Slide 1 — Headline',     'name' => 'hero_slide0_title', 'type' => 'text',     'placeholder' => 'Design for Lover, wildly loved.',       'instructions' => 'Bold headline on the first hero slide.'),
            array('key' => 'field_dc_hero_slide0_desc',   'label' => 'Slide 1 — Description',  'name' => 'hero_slide0_desc',  'type' => 'textarea', 'rows' => 3,  'placeholder' => 'Sustainable coffee products…',          'instructions' => 'Short description below the headline.'),
            array('key' => 'field_dc_hero1_tab',          'label' => 'Hero Slide 2',           'name' => '',                  'type' => 'tab'),
            array('key' => 'field_dc_hero_slide1_title',  'label' => 'Slide 2 — Headline',     'name' => 'hero_slide1_title', 'type' => 'text',     'placeholder' => 'SUSTAINABLE AGRICULTURE'),
            array('key' => 'field_dc_hero_slide1_desc',   'label' => 'Slide 2 — Description',  'name' => 'hero_slide1_desc',  'type' => 'textarea', 'rows' => 3,  'placeholder' => 'We cultivate fair relationships…'),
            array('key' => 'field_dc_hero2_tab',          'label' => 'Hero Slide 3',           'name' => '',                  'type' => 'tab'),
            array('key' => 'field_dc_hero_slide2_title',  'label' => 'Slide 3 — Headline',     'name' => 'hero_slide2_title', 'type' => 'text',     'placeholder' => 'Education, Workshop & Tours'),
            array('key' => 'field_dc_hero_slide2_desc',   'label' => 'Slide 3 — Description',  'name' => 'hero_slide2_desc',  'type' => 'textarea', 'rows' => 3,  'placeholder' => 'Through farm education, immersive tours…'),
        ),
        'location' => array(array(array('param' => 'page_type', 'operator' => '==', 'value' => 'front_page'))),
        'menu_order' => 0, 'position' => 'normal', 'style' => 'default', 'label_placement' => 'top', 'instruction_placement' => 'label',
    ));

    // --- About page ---
    acf_add_local_field_group(array(
        'key'    => 'group_dc_about',
        'title'  => '☕ About Page Content',
        'fields' => array(
            array('key' => 'field_dc_about_heading', 'label' => 'Main Heading', 'name' => 'about_heading', 'type' => 'text',     'instructions' => 'Large h1 on the About page.',         'placeholder' => 'Designer Coffee: Where Coffee is Loved in Its Most Primitive Form'),
            array('key' => 'field_dc_about_body',    'label' => 'Body Text',    'name' => 'about_body',    'type' => 'textarea', 'instructions' => 'Paragraph text below the heading.', 'rows' => 5, 'placeholder' => 'At Designer Coffee, coffee is not just a drink…'),
        ),
        'location' => array(array(array('param' => 'page_template', 'operator' => '==', 'value' => 'pages/about/template-about.php'))),
        'menu_order' => 0, 'position' => 'normal', 'style' => 'default', 'label_placement' => 'top',
    ));
}


