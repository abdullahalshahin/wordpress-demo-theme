<?php
/**
 * Admin Page: General
 *
 * Displays the theme's general administration page.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}
?>

<?php
$theme = wp_get_theme();
$customize_url = admin_url('customize.php');
$quick_links = array(
    array(
        'title' => __('Header Settings', 'wordpress-demo-theme'),
        'description' => __('Configure the site header, navigation, and header button.', 'wordpress-demo-theme'),
        'url' => admin_url('admin.php?page=wordpress-demo-theme-header'),
        'icon' => 'dashicons-menu-alt3',
    ),
    array(
        'title' => __('Footer Settings', 'wordpress-demo-theme'),
        'description' => __('Control footer visibility and build custom footer sections.', 'wordpress-demo-theme'),
        'url' => admin_url('admin.php?page=wordpress-demo-theme-footer'),
        'icon' => 'dashicons-align-full-width',
    ),
    array(
        'title' => __('Theme Settings', 'wordpress-demo-theme'),
        'description' => __('Manage the theme options available for your website.', 'wordpress-demo-theme'),
        'url' => admin_url('admin.php?page=wordpress-demo-theme-settings'),
        'icon' => 'dashicons-admin-generic',
    ),
);
?>

<div class="wordpress-demo-theme-admin-page wordpress-demo-theme-dashboard">
    <div class="wordpress-demo-theme-dashboard-hero">
        <div>
            <span class="wordpress-demo-theme-dashboard-label"><?php esc_html_e('WordPress Demo Theme', 'wordpress-demo-theme'); ?></span>
            <h1><?php esc_html_e('Welcome to your theme dashboard', 'wordpress-demo-theme'); ?></h1>
            <p><?php esc_html_e('Use this page to quickly access theme settings and review your current website environment.', 'wordpress-demo-theme'); ?></p>

            <div class="wordpress-demo-theme-dashboard-actions">
                <a href="<?php echo esc_url($customize_url); ?>" class="button button-primary"><?php esc_html_e('Customize Theme', 'wordpress-demo-theme'); ?></a>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="button" target="_blank" rel="noopener noreferrer"><?php esc_html_e('View Website', 'wordpress-demo-theme'); ?></a>
            </div>
        </div>

        <img src="<?php echo esc_url(WORDPRESS_DEMO_THEME_URI . 'assets/images/icon.png'); ?>" alt="" width="96" height="96">
    </div>

    <div class="wordpress-demo-theme-dashboard-grid">
        <section class="wordpress-demo-theme-dashboard-main" aria-labelledby="wordpress-demo-theme-quick-settings">
            <div class="wordpress-demo-theme-dashboard-heading">
                <h2 id="wordpress-demo-theme-quick-settings"><?php esc_html_e('Quick Settings', 'wordpress-demo-theme'); ?></h2>
                <p><?php esc_html_e('Choose a section to start configuring your theme.', 'wordpress-demo-theme'); ?></p>
            </div>

            <div class="wordpress-demo-theme-dashboard-links">
                <?php foreach($quick_links as $quick_link) : ?>
                    <a href="<?php echo esc_url($quick_link['url']); ?>" class="wordpress-demo-theme-dashboard-link">
                        <span class="dashicons <?php echo esc_attr($quick_link['icon']); ?>" aria-hidden="true"></span>
                        <span>
                            <strong><?php echo esc_html($quick_link['title']); ?></strong>
                            <small><?php echo esc_html($quick_link['description']); ?></small>
                        </span>
                        <span class="dashicons dashicons-arrow-right-alt2" aria-hidden="true"></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <aside class="wordpress-demo-theme-dashboard-sidebar" aria-labelledby="wordpress-demo-theme-site-information">
            <div class="wordpress-demo-theme-dashboard-heading">
                <h2 id="wordpress-demo-theme-site-information"><?php esc_html_e('Site Information', 'wordpress-demo-theme'); ?></h2>
                <p><?php esc_html_e('Current environment details.', 'wordpress-demo-theme'); ?></p>
            </div>

            <dl class="wordpress-demo-theme-dashboard-information">
                <div><dt><?php esc_html_e('Theme version', 'wordpress-demo-theme'); ?></dt><dd><?php echo esc_html($theme->get('Version')); ?></dd></div>
                <div><dt><?php esc_html_e('WordPress version', 'wordpress-demo-theme'); ?></dt><dd><?php echo esc_html(get_bloginfo('version')); ?></dd></div>
                <div><dt><?php esc_html_e('PHP version', 'wordpress-demo-theme'); ?></dt><dd><?php echo esc_html(PHP_VERSION); ?></dd></div>
                <div><dt><?php esc_html_e('Site language', 'wordpress-demo-theme'); ?></dt><dd><?php echo esc_html(get_locale()); ?></dd></div>
            </dl>
        </aside>
    </div>
</div>
