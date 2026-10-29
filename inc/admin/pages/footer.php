<?php
/**
 * Admin Page: Footer
 *
 * Displays the theme's footer administration page.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

$is_footer_copyright_visible = (bool) get_theme_mod('wordpress_demo_theme_is_footer_copyright_visible', true);
$is_scroll_to_top_button_visible = (bool) get_theme_mod('wordpress_demo_theme_is_scroll_to_top_button_visible', true);
$is_custom_footer_visible = (bool) get_theme_mod('wordpress_demo_theme_is_custom_footer_visible', false);
$custom_footer_sections = get_theme_mod('wordpress_demo_theme_custom_footer_sections', array());
$custom_footer_sections = is_array($custom_footer_sections) ? $custom_footer_sections : array();
$navigation_menus = wp_get_nav_menus(array('orderby' => 'name'));
$site_logo_id = absint(get_theme_mod('custom_logo', 0));

if(!function_exists('wordpress_demo_theme_prepare_footer_builder_images')) {
    function wordpress_demo_theme_prepare_footer_builder_images($sections) {
        foreach($sections as &$section) {
            if(empty($section['blocks']) || !is_array($section['blocks'])) {
                $section['blocks'] = array();
                continue;
            }

            foreach($section['blocks'] as &$block) {
                if(isset($block['image_id'])) {
                    $block['image_url'] = wp_get_attachment_image_url(absint($block['image_id']), 'thumbnail') ?: '';
                }

                if(empty($block['items']) || !is_array($block['items'])) {
                    continue;
                }

                foreach($block['items'] as &$item) {
                    $item['image_url'] = wp_get_attachment_image_url(absint($item['image_id'] ?? 0), 'thumbnail') ?: '';
                }
                unset($item);
            }
            unset($block);
        }
        unset($section);

        return $sections;
    }
}

$footer_builder_data = array(
    'sections' => wordpress_demo_theme_prepare_footer_builder_images($custom_footer_sections),
    'menus' => array_map(function($menu) {
        return array(
            'id' => absint($menu->term_id),
            'name' => $menu->name
        );
    }, $navigation_menus),
    'site_logo' => array(
        'id' => $site_logo_id,
        'url' => $site_logo_id ? wp_get_attachment_image_url($site_logo_id, 'thumbnail') : WORDPRESS_DEMO_THEME_URI . 'assets/images/logo.png'
    ),
    'labels' => array(
        'section' => __('Section', 'wordpress-demo-theme'),
        'section_name' => __('Section name', 'wordpress-demo-theme'),
        'add_section' => __('Add Section', 'wordpress-demo-theme'),
        'remove_section' => __('Remove Section', 'wordpress-demo-theme'),
        'add_block' => __('Add Content Block', 'wordpress-demo-theme'),
        'remove_block' => __('Remove Block', 'wordpress-demo-theme'),
        'block_type' => __('Content type', 'wordpress-demo-theme'),
        'move_up' => __('Move Up', 'wordpress-demo-theme'),
        'move_down' => __('Move Down', 'wordpress-demo-theme'),
        'expand' => __('Expand or collapse', 'wordpress-demo-theme'),
        'image' => __('Logo / Image', 'wordpress-demo-theme'),
        'heading' => __('Heading', 'wordpress-demo-theme'),
        'description' => __('Description', 'wordpress-demo-theme'),
        'social_links' => __('Social Links', 'wordpress-demo-theme'),
        'navigation_menu' => __('Navigation Menu', 'wordpress-demo-theme'),
        'contact_details' => __('Contact Details', 'wordpress-demo-theme'),
        'custom_html' => __('Custom HTML', 'wordpress-demo-theme'),
        'use_site_logo' => __('Use existing WordPress Site Logo', 'wordpress-demo-theme'),
        'select_image' => __('Select / Upload Image', 'wordpress-demo-theme'),
        'replace_image' => __('Replace Image', 'wordpress-demo-theme'),
        'remove_image' => __('Remove Image', 'wordpress-demo-theme'),
        'heading_text' => __('Heading text', 'wordpress-demo-theme'),
        'description_text' => __('Description text', 'wordpress-demo-theme'),
        'html_code' => __('HTML code', 'wordpress-demo-theme'),
        'select_menu' => __('Select a menu', 'wordpress-demo-theme'),
        'add_social_link' => __('Add Social Link', 'wordpress-demo-theme'),
        'remove_social_link' => __('Remove Social Link', 'wordpress-demo-theme'),
        'add_contact' => __('Add Contact Item', 'wordpress-demo-theme'),
        'remove_contact' => __('Remove Contact Item', 'wordpress-demo-theme'),
        'url' => __('URL', 'wordpress-demo-theme'),
        'label' => __('Label', 'wordpress-demo-theme'),
        'no_sections' => __('No custom footer sections yet.', 'wordpress-demo-theme'),
        'confirm_remove_section' => __('Remove this footer section?', 'wordpress-demo-theme'),
        'confirm_remove_block' => __('Remove this content block?', 'wordpress-demo-theme'),
        'confirm_remove_item' => __('Remove this item?', 'wordpress-demo-theme'),
        'media_title' => __('Select an image', 'wordpress-demo-theme'),
        'media_button' => __('Use this image', 'wordpress-demo-theme')
    )
);
?>

<div class="wrap wordpress-demo-theme-admin-page">
    <h1><?php esc_html_e('Footer Settings', 'wordpress-demo-theme'); ?></h1>

    <?php if(isset($_GET['footer_updated']) && sanitize_key(wp_unslash($_GET['footer_updated'])) === '1') : ?>
        <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Footer settings saved.', 'wordpress-demo-theme'); ?></p></div>
    <?php elseif(isset($_GET['footer_error'])) : ?>
        <div class="notice notice-error is-dismissible"><p><?php esc_html_e('Footer settings could not be saved. Please try again.', 'wordpress-demo-theme'); ?></p></div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" data-footer-settings-form>
        <input type="hidden" name="action" value="wordpress_demo_theme_save_footer_settings">
        <input type="hidden" name="footer_sections" value="<?php echo esc_attr(wp_json_encode($custom_footer_sections)); ?>" data-footer-sections-input>
        <?php wp_nonce_field('wordpress_demo_theme_save_footer_settings'); ?>

        <div class="wordpress-demo-theme-settings-card wordpress-demo-theme-display-settings">
            <h2><?php esc_html_e('Footer Display', 'wordpress-demo-theme'); ?></h2>

            <label class="wordpress-demo-theme-toggle-row">
                <span>
                    <strong><?php esc_html_e('Show Copyright', 'wordpress-demo-theme'); ?></strong>
                    <small><?php esc_html_e('Display the copyright text in the footer.', 'wordpress-demo-theme'); ?></small>
                </span>
                <input type="checkbox" name="is_footer_copyright_visible" value="1" <?php checked($is_footer_copyright_visible); ?>>
            </label>

            <label class="wordpress-demo-theme-toggle-row">
                <span>
                    <strong><?php esc_html_e('Show Scroll to Top Button', 'wordpress-demo-theme'); ?></strong>
                    <small><?php esc_html_e('Display the floating button used to return to the top of the page.', 'wordpress-demo-theme'); ?></small>
                </span>
                <input type="checkbox" name="is_scroll_to_top_button_visible" value="1" <?php checked($is_scroll_to_top_button_visible); ?>>
            </label>

            <label class="wordpress-demo-theme-toggle-row">
                <span>
                    <strong><?php esc_html_e('Enable Custom Footer', 'wordpress-demo-theme'); ?></strong>
                    <small><?php esc_html_e('Replace the default footer content with the sections below.', 'wordpress-demo-theme'); ?></small>
                </span>
                <input type="checkbox" name="is_custom_footer_visible" value="1" <?php checked($is_custom_footer_visible); ?> data-custom-footer-toggle>
            </label>
        </div>

        <div class="wordpress-demo-theme-settings-card" data-footer-builder-card <?php echo $is_custom_footer_visible ? '' : 'hidden'; ?>>
            <div class="wordpress-demo-theme-card-header">
                <div>
                    <h2><?php esc_html_e('Custom Footer Builder', 'wordpress-demo-theme'); ?></h2>
                    <p><?php esc_html_e('Add sections and arrange their content using the simple controls below.', 'wordpress-demo-theme'); ?></p>
                </div>
                <button type="button" class="button button-secondary" data-add-footer-section><?php esc_html_e('Add Section', 'wordpress-demo-theme'); ?></button>
            </div>

            <div data-footer-builder></div>
        </div>

        <?php submit_button(__('Save Changes', 'wordpress-demo-theme')); ?>
    </form>

    <script type="application/json" data-footer-builder-data><?php echo wp_json_encode($footer_builder_data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
</div>
