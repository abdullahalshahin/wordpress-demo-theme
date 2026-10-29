<?php
/**
 * Core: Theme Setup
 *
 * Registers theme features, navigation menus,
 * and translation support.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

if(!function_exists('wordpress_demo_theme_setup')) {
    function wordpress_demo_theme_setup() {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('automatic-feed-links');
        add_theme_support('customize-selective-refresh-widgets');
        add_theme_support('responsive-embeds');
        add_theme_support('align-wide');

        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script'
        ));

        add_theme_support('custom-logo', array(
            'flex-height' => true,
            'flex-width' => true
        ));

        register_nav_menus(array(
            'primary' => __('Primary Menu', 'wordpress-demo-theme'),
            'footer' => __('Footer Menu', 'wordpress-demo-theme')
        ));

        load_theme_textdomain('wordpress-demo-theme', WORDPRESS_DEMO_THEME_PATH . 'languages');
    }
}

add_action('after_setup_theme', 'wordpress_demo_theme_setup');
