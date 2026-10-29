<?php
/**
 * Core: Asset Management
 *
 * Enqueues frontend, user dashboard, and admin dashboard
 * stylesheets and JavaScript files.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

if(!function_exists('wordpress_demo_theme_enqueue_assets')) {
    function wordpress_demo_theme_enqueue_assets() {
        wp_enqueue_style('wordpress-demo-theme-style', WORDPRESS_DEMO_THEME_URI . 'assets/css/style.css', array(), filemtime(WORDPRESS_DEMO_THEME_PATH . 'assets/css/style.css'));

        $body_font = get_theme_mod('wordpress_demo_theme_body_font', 'figtree');
        $heading_font = get_theme_mod('wordpress_demo_theme_heading_font', 'figtree');
        $base_font_size = wordpress_demo_theme_sanitize_font_size(get_theme_mod('wordpress_demo_theme_base_font_size', 16));
        $typography_css = ':root{--font-body:' . wordpress_demo_theme_get_font_stack($body_font) . ';--font-heading:' . wordpress_demo_theme_get_font_stack($heading_font) . ';--font-size-base:' . $base_font_size . 'px;}';

        wp_add_inline_style('wordpress-demo-theme-style', $typography_css);
        wp_enqueue_script('wordpress-demo-theme-init', WORDPRESS_DEMO_THEME_URI . 'assets/js/theme-init.js', array(), WORDPRESS_DEMO_THEME_VERSION, array('in_footer' => false));
        wp_enqueue_script('wordpress-demo-theme-script', WORDPRESS_DEMO_THEME_URI . 'assets/js/main.js', array(), filemtime(WORDPRESS_DEMO_THEME_PATH . 'assets/js/main.js'), array('in_footer' => true));
    }
}

if(!function_exists('wordpress_demo_theme_enqueue_admin_assets')) {
    function wordpress_demo_theme_enqueue_admin_assets() {
        wp_enqueue_style('wordpress-demo-theme-admin-style', WORDPRESS_DEMO_THEME_URI . 'assets/css/app.css', array(), filemtime(WORDPRESS_DEMO_THEME_PATH . 'assets/css/app.css'));
        wp_enqueue_script('wordpress-demo-theme-admin-app', WORDPRESS_DEMO_THEME_URI . 'assets/js/app.js', array(), filemtime(WORDPRESS_DEMO_THEME_PATH . 'assets/js/app.js'), array('in_footer' => true));

        $admin_page = isset($_GET['page']) && is_string($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';

        if($admin_page === 'wordpress-demo-theme-footer') {
            wp_enqueue_media();
        }
    }
}

add_action('wp_enqueue_scripts', 'wordpress_demo_theme_enqueue_assets');
add_action('admin_enqueue_scripts', 'wordpress_demo_theme_enqueue_admin_assets');
