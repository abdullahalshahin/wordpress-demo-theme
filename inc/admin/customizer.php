<?php
/**
 * Admin: Customizer
 *
 * Provides theme settings and controls for the
 * WordPress Customizer.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

if(!function_exists('wordpress_demo_theme_get_font_choices')) {
    function wordpress_demo_theme_get_font_choices() {
        $choices = array(
            'figtree' => __('Figtree', 'wordpress-demo-theme'),
            'system' => __('System Sans Serif', 'wordpress-demo-theme'),
        );
        $font_families = get_posts(array(
            'post_type' => 'wp_font_family',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'no_found_rows' => true,
        ));

        foreach($font_families as $font_family) {
            $settings = json_decode($font_family->post_content, true);

            if(!empty($settings['fontFamily']) && is_string($settings['fontFamily'])) {
                $choices['library-' . $font_family->ID] = $font_family->post_title;
            }
        }

        return $choices;
    }
}

if(!function_exists('wordpress_demo_theme_sanitize_font_choice')) {
    function wordpress_demo_theme_sanitize_font_choice($font) {
        $font = sanitize_key($font);
        $choices = wordpress_demo_theme_get_font_choices();

        return isset($choices[$font]) ? $font : 'figtree';
    }
}

if(!function_exists('wordpress_demo_theme_sanitize_font_size')) {
    function wordpress_demo_theme_sanitize_font_size($font_size) {
        return max(14, min(20, absint($font_size)));
    }
}

if(!function_exists('wordpress_demo_theme_get_font_stack')) {
    function wordpress_demo_theme_get_font_stack($font) {
        $font = wordpress_demo_theme_sanitize_font_choice($font);

        if($font === 'figtree') {
            return '"Figtree", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
        }

        if($font === 'system') {
            return 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
        }

        $font_id = strpos($font, 'library-') === 0 ? absint(substr($font, 8)) : 0;
        $font_post = $font_id ? get_post($font_id) : null;

        if(!$font_post instanceof WP_Post || $font_post->post_type !== 'wp_font_family' || $font_post->post_status !== 'publish') {
            return '"Figtree", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
        }

        $settings = json_decode($font_post->post_content, true);
        $font_family = !empty($settings['fontFamily']) && is_string($settings['fontFamily']) ? $settings['fontFamily'] : '';
        $font_family = class_exists('WP_Font_Utils') ? WP_Font_Utils::sanitize_font_family($font_family) : sanitize_text_field($font_family);

        return $font_family ?: '"Figtree", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
    }
}

if(!function_exists('wordpress_demo_theme_customize_register')) {
    function wordpress_demo_theme_customize_register($wp_customize) {
        $wp_customize->add_setting('wordpress_demo_theme_dark_logo', array(
            'default' => 0,
            'sanitize_callback' => 'absint',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'wordpress_demo_theme_dark_logo', array(
            'label' => __('Dark Mode Logo', 'wordpress-demo-theme'),
            'description' => __('Select the logo displayed when dark mode is active. The standard logo is used when no dark logo is selected.', 'wordpress-demo-theme'),
            'section' => 'title_tagline',
            'mime_type' => 'image',
            'priority' => 9,
        )));

        $wp_customize->add_section('wordpress_demo_theme_typography', array(
            'title' => __('Typography', 'wordpress-demo-theme'),
            'description' => __('Configure the fonts used throughout the website.', 'wordpress-demo-theme'),
            'priority' => 35,
        ));

        $wp_customize->add_setting('wordpress_demo_theme_body_font', array(
            'default' => 'figtree',
            'sanitize_callback' => 'wordpress_demo_theme_sanitize_font_choice',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('wordpress_demo_theme_body_font', array(
            'label' => __('Body Font', 'wordpress-demo-theme'),
            'description' => __('Used for paragraphs, navigation, forms, and general interface text. Installed WordPress fonts appear here.', 'wordpress-demo-theme'),
            'section' => 'wordpress_demo_theme_typography',
            'type' => 'select',
            'choices' => wordpress_demo_theme_get_font_choices(),
        ));

        $wp_customize->add_setting('wordpress_demo_theme_heading_font', array(
            'default' => 'figtree',
            'sanitize_callback' => 'wordpress_demo_theme_sanitize_font_choice',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('wordpress_demo_theme_heading_font', array(
            'label' => __('Heading Font', 'wordpress-demo-theme'),
            'description' => __('Used for headings from H1 through H6. Installed WordPress fonts appear here.', 'wordpress-demo-theme'),
            'section' => 'wordpress_demo_theme_typography',
            'type' => 'select',
            'choices' => wordpress_demo_theme_get_font_choices(),
        ));

        $wp_customize->add_setting('wordpress_demo_theme_base_font_size', array(
            'default' => 16,
            'sanitize_callback' => 'wordpress_demo_theme_sanitize_font_size',
            'transport' => 'refresh',
        ));

        $wp_customize->add_control('wordpress_demo_theme_base_font_size', array(
            'label' => __('Base Font Size', 'wordpress-demo-theme'),
            'description' => __('Sets the default website text size in pixels.', 'wordpress-demo-theme'),
            'section' => 'wordpress_demo_theme_typography',
            'type' => 'number',
            'input_attrs' => array(
                'min' => 14,
                'max' => 20,
                'step' => 1,
            ),
        ));

        if(isset($wp_customize->selective_refresh)) {
            $wp_customize->selective_refresh->add_partial('wordpress_demo_theme_header_logo', array(
                'settings' => array('custom_logo', 'wordpress_demo_theme_dark_logo'),
                'selector' => '.header-site-branding',
                'container_inclusive' => true,
                'fallback_refresh' => true,
            ));

            $wp_customize->selective_refresh->add_partial('wordpress_demo_theme_primary_menu', array(
                'settings' => array('nav_menu_locations[primary]'),
                'selector' => '.primary-navigation',
                'container_inclusive' => true,
                'fallback_refresh' => true,
            ));
        }
    }
}

add_action('customize_register', 'wordpress_demo_theme_customize_register');
