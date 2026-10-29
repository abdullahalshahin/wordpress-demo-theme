<?php
/**
 * Admin: Settings
 *
 * Registers settings and fields for the theme's
 * administration pages.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

if(!function_exists('wordpress_demo_theme_save_footer_settings')) {
    function wordpress_demo_theme_save_footer_settings() {
        if(!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have permission to update these settings.', 'wordpress-demo-theme'));
        }

        check_admin_referer('wordpress_demo_theme_save_footer_settings');

        $sections_json = isset($_POST['footer_sections']) && is_string($_POST['footer_sections']) ? wp_unslash($_POST['footer_sections']) : '[]';
        $sections = json_decode($sections_json, true);

        if(!is_array($sections)) {
            $redirect_url = add_query_arg(array(
                'page' => 'wordpress-demo-theme-footer',
                'footer_error' => 'invalid_data'
            ), admin_url('admin.php'));

            wp_safe_redirect($redirect_url);
            exit;
        }

        set_theme_mod('wordpress_demo_theme_is_footer_copyright_visible', isset($_POST['is_footer_copyright_visible']));
        set_theme_mod('wordpress_demo_theme_is_scroll_to_top_button_visible', isset($_POST['is_scroll_to_top_button_visible']));
        set_theme_mod('wordpress_demo_theme_is_custom_footer_visible', isset($_POST['is_custom_footer_visible']));
        set_theme_mod('wordpress_demo_theme_custom_footer_sections', wordpress_demo_theme_sanitize_footer_sections($sections));

        $redirect_url = add_query_arg(array(
            'page' => 'wordpress-demo-theme-footer',
            'footer_updated' => '1'
        ), admin_url('admin.php'));

        wp_safe_redirect($redirect_url);
        exit;
    }
}

add_action('admin_post_wordpress_demo_theme_save_footer_settings', 'wordpress_demo_theme_save_footer_settings');

if(!function_exists('wordpress_demo_theme_save_header_settings')) {
    function wordpress_demo_theme_save_header_settings() {
        if(!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have permission to update these settings.', 'wordpress-demo-theme'));
        }

        check_admin_referer('wordpress_demo_theme_save_header_settings');

        $available_designs = wordpress_demo_theme_get_available_designs('header');
        $header_design = isset($_POST['header_design']) && is_string($_POST['header_design']) ? sanitize_key(wp_unslash($_POST['header_design'])) : 'design-1';
        $header_design = isset($available_designs[$header_design]) ? $header_design : 'design-1';
        $header_button_title = isset($_POST['header_button_title']) && is_string($_POST['header_button_title']) ? sanitize_text_field(wp_unslash($_POST['header_button_title'])) : '';
        $header_button_url = isset($_POST['header_button_url']) && is_string($_POST['header_button_url']) ? esc_url_raw(wp_unslash($_POST['header_button_url'])) : '';

        set_theme_mod('wordpress_demo_theme_header_design', $header_design);
        set_theme_mod('wordpress_demo_theme_is_header_button_visible', isset($_POST['is_header_button_visible']));
        set_theme_mod('wordpress_demo_theme_is_header_search_icon_visible', isset($_POST['is_header_search_icon_visible']));
        set_theme_mod('wordpress_demo_theme_is_header_dark_mode_toggle_visible', isset($_POST['is_header_dark_mode_toggle_visible']));
        set_theme_mod('wordpress_demo_theme_header_button_title', $header_button_title);
        set_theme_mod('wordpress_demo_theme_header_button_url', $header_button_url ?: '#');

        wp_safe_redirect(add_query_arg(array(
            'page' => 'wordpress-demo-theme-header',
            'header_updated' => '1'
        ), admin_url('admin.php')));
        exit;
    }
}

add_action('admin_post_wordpress_demo_theme_save_header_settings', 'wordpress_demo_theme_save_header_settings');
