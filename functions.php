<?php
/**
 * Theme: Functions
 *
 * Initializes the WordPress Demo Theme and loads
 * its core functionality, helpers, and modules.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

define('WORDPRESS_DEMO_THEME_VERSION', wp_get_theme()->get('Version'));
define('WORDPRESS_DEMO_THEME_PATH', trailingslashit(get_template_directory()));
define('WORDPRESS_DEMO_THEME_URI', trailingslashit(get_template_directory_uri()));

require_once WORDPRESS_DEMO_THEME_PATH . 'inc/core/theme-setup.php';
require_once WORDPRESS_DEMO_THEME_PATH . 'inc/core/enqueue-assets.php';
require_once WORDPRESS_DEMO_THEME_PATH . 'inc/core/hooks.php';
require_once WORDPRESS_DEMO_THEME_PATH . 'inc/helpers.php';
require_once WORDPRESS_DEMO_THEME_PATH . 'inc/user/authentication.php';
require_once WORDPRESS_DEMO_THEME_PATH . 'inc/user/bookmarks.php';
require_once WORDPRESS_DEMO_THEME_PATH . 'inc/user/likes.php';
require_once WORDPRESS_DEMO_THEME_PATH . 'inc/admin/customizer.php';

if(is_admin()) {
    require_once WORDPRESS_DEMO_THEME_PATH . 'inc/admin/admin-init.php';
}
