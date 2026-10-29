<?php
/**
 * Admin Page: Header
 *
 * Displays the theme's header administration page.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

$available_designs = wordpress_demo_theme_get_available_designs('header');
$selected_header_design = get_theme_mod('wordpress_demo_theme_header_design', 'design-1');
$is_header_button_visible = (bool) get_theme_mod('wordpress_demo_theme_is_header_button_visible', true);
$is_header_search_icon_visible = (bool) get_theme_mod('wordpress_demo_theme_is_header_search_icon_visible', true);
$is_header_dark_mode_toggle_visible = (bool) get_theme_mod('wordpress_demo_theme_is_header_dark_mode_toggle_visible', true);
$header_button_title = get_theme_mod('wordpress_demo_theme_header_button_title', __('Button', 'wordpress-demo-theme'));
$header_button_url = get_theme_mod('wordpress_demo_theme_header_button_url', '#');
?>

<div class="wrap wordpress-demo-theme-admin-page">
    <h1><?php esc_html_e('Header Settings', 'wordpress-demo-theme'); ?></h1>

    <?php if(isset($_GET['header_updated']) && sanitize_key(wp_unslash($_GET['header_updated'])) === '1') : ?>
        <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Header settings saved.', 'wordpress-demo-theme'); ?></p></div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="wordpress_demo_theme_save_header_settings">
        <?php wp_nonce_field('wordpress_demo_theme_save_header_settings'); ?>

        <div class="wordpress-demo-theme-settings-card">
            <div class="wordpress-demo-theme-card-header">
                <div>
                    <h2><?php esc_html_e('Header Layout', 'wordpress-demo-theme'); ?></h2>
                    <p><?php esc_html_e('Choose how the logo, navigation, and actions are aligned on larger screens.', 'wordpress-demo-theme'); ?></p>
                </div>
            </div>

            <div class="wordpress-demo-theme-design-options">
                <?php foreach($available_designs as $design_key => $design_name) : ?>
                    <label class="wordpress-demo-theme-design-option">
                        <input type="radio" name="header_design" value="<?php echo esc_attr($design_key); ?>" <?php checked($selected_header_design, $design_key); ?>>
                        <span class="wordpress-demo-theme-design-preview wordpress-demo-theme-<?php echo esc_attr($design_key); ?>" aria-hidden="true">
                            <i></i><b></b><em></em>
                        </span>
                        <strong><?php echo esc_html($design_name); ?></strong>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="wordpress-demo-theme-settings-card wordpress-demo-theme-display-settings">
            <h2><?php esc_html_e('Header Elements', 'wordpress-demo-theme'); ?></h2>

            <label class="wordpress-demo-theme-toggle-row">
                <span>
                    <strong><?php esc_html_e('Show Header Button', 'wordpress-demo-theme'); ?></strong>
                    <small><?php esc_html_e('Display the call-to-action button in the header.', 'wordpress-demo-theme'); ?></small>
                </span>
                <input type="checkbox" name="is_header_button_visible" value="1" <?php checked($is_header_button_visible); ?>>
            </label>

            <label class="wordpress-demo-theme-toggle-row">
                <span>
                    <strong><?php esc_html_e('Show Search', 'wordpress-demo-theme'); ?></strong>
                    <small><?php esc_html_e('Display the search button in the header.', 'wordpress-demo-theme'); ?></small>
                </span>
                <input type="checkbox" name="is_header_search_icon_visible" value="1" <?php checked($is_header_search_icon_visible); ?>>
            </label>

            <label class="wordpress-demo-theme-toggle-row">
                <span>
                    <strong><?php esc_html_e('Show Dark Mode Toggle', 'wordpress-demo-theme'); ?></strong>
                    <small><?php esc_html_e('Allow visitors to switch between light and dark modes.', 'wordpress-demo-theme'); ?></small>
                </span>
                <input type="checkbox" name="is_header_dark_mode_toggle_visible" value="1" <?php checked($is_header_dark_mode_toggle_visible); ?>>
            </label>
        </div>

        <div class="wordpress-demo-theme-settings-card">
            <div class="wordpress-demo-theme-card-header">
                <div>
                    <h2><?php esc_html_e('Header Button', 'wordpress-demo-theme'); ?></h2>
                    <p><?php esc_html_e('Set the button text and destination. Use /login to show a Dashboard button after login.', 'wordpress-demo-theme'); ?></p>
                </div>
            </div>

            <div class="wordpress-demo-theme-header-fields">
                <label>
                    <span><?php esc_html_e('Button title', 'wordpress-demo-theme'); ?></span>
                    <input type="text" name="header_button_title" value="<?php echo esc_attr($header_button_title); ?>" class="regular-text" maxlength="80">
                </label>

                <label>
                    <span><?php esc_html_e('Button URL', 'wordpress-demo-theme'); ?></span>
                    <input type="text" name="header_button_url" value="<?php echo esc_attr($header_button_url); ?>" class="regular-text" placeholder="/login">
                </label>
            </div>
        </div>

        <div class="wordpress-demo-theme-settings-card wordpress-demo-theme-header-menu-card">
            <div>
                <h2><?php esc_html_e('Navigation Menu', 'wordpress-demo-theme'); ?></h2>
                <p><?php esc_html_e('Assign and manage the menu displayed in the Primary Menu location.', 'wordpress-demo-theme'); ?></p>
            </div>
            <a href="<?php echo esc_url(admin_url('nav-menus.php?action=locations')); ?>" class="button button-secondary"><?php esc_html_e('Manage Menus', 'wordpress-demo-theme'); ?></a>
        </div>

        <?php submit_button(__('Save Changes', 'wordpress-demo-theme')); ?>
    </form>
</div>
