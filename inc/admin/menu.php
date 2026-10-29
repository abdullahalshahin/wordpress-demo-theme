
<?php
/**
 * Admin: Menu
 *
 * Registers and renders theme administration menus.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

if(!function_exists('wordpress_demo_theme_admin_menus')) {
    function wordpress_demo_theme_admin_menus() {
        $admin_menus = array(
            array(
                'title' => __('WP Demo Theme', 'wordpress-demo-theme'),
                'slug' => 'wordpress-demo-theme',
                'file_path' => 'inc/admin/pages/general.php',
                'icon' => WORDPRESS_DEMO_THEME_URI . 'assets/images/icon.png',
                'submenus' => array(
                    array(
                        'title' => __('General', 'wordpress-demo-theme'),
                        'slug' => 'wordpress-demo-theme',
                        'file_path' => 'inc/admin/pages/general.php',
                    ),
                    array(
                        'title' => __('Header', 'wordpress-demo-theme'),
                        'slug' => 'wordpress-demo-theme-header',
                        'file_path' => 'inc/admin/pages/header.php',
                    ),
                    array(
                        'title' => __('Footer', 'wordpress-demo-theme'),
                        'slug' => 'wordpress-demo-theme-footer',
                        'file_path' => 'inc/admin/pages/footer.php',
                    ),
                    array(
                        'title' => __('Settings', 'wordpress-demo-theme'),
                        'slug' => 'wordpress-demo-theme-settings',
                        'file_path' => 'inc/admin/pages/settings.php',
                    ),
                ),
            ),
        );

        return $admin_menus;
    }
}

if(!function_exists('wordpress_demo_theme_render_admin_page')) {
    function wordpress_demo_theme_render_admin_page($file_path) {
        if(!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have permission to access this page.', 'wordpress-demo-theme'));
        }

        $menus = wordpress_demo_theme_admin_menus();
        $allowed_files = array();

        foreach($menus as $menu) {
            $allowed_files[] = $menu['file_path'];

            foreach($menu['submenus'] as $submenu) {
                $allowed_files[] = $submenu['file_path'];
            }
        }

        if(!in_array($file_path, $allowed_files, true)) {
            return;
        }

        $file = WORDPRESS_DEMO_THEME_PATH . $file_path;

        if(is_file($file)) {
            require $file;
        }
    }
}

if(!function_exists('wordpress_demo_theme_register_admin_menu')) {
    function wordpress_demo_theme_register_admin_menu() {
        $menus = wordpress_demo_theme_admin_menus();
        $capability = 'manage_options';
        $position = 59;

        foreach($menus as $menu) {
            $main_callback = function() use ($menu) {
                wordpress_demo_theme_render_admin_page($menu['file_path']);
            };

            add_menu_page(
                $menu['title'],
                $menu['title'],
                $capability,
                $menu['slug'],
                $main_callback,
                $menu['icon'],
                $position++,
            );

            foreach($menu['submenus'] as $submenu) {
                $same_page = $submenu['slug'] === $menu['slug'];

                $callback = $same_page ? $main_callback : function() use ($submenu) {
                    wordpress_demo_theme_render_admin_page($submenu['file_path']);
                };

                add_submenu_page(
                    $menu['slug'],
                    $submenu['title'],
                    $submenu['title'],
                    $capability,
                    $submenu['slug'],
                    $callback
                );
            }
        }
    }
}

add_action('admin_menu', 'wordpress_demo_theme_register_admin_menu');
