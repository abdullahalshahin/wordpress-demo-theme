<?php
/**
 * Admin: Initialization
 *
 * Loads the theme's WordPress administration modules.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

require_once WORDPRESS_DEMO_THEME_PATH . 'inc/admin/sanitize.php';
require_once WORDPRESS_DEMO_THEME_PATH . 'inc/admin/settings.php';
require_once WORDPRESS_DEMO_THEME_PATH . 'inc/admin/menu.php';
