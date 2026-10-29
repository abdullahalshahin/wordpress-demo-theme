<?php
/**
 * Layout: Header
 *
 * Displays site branding, navigation, dropdown menus,
 * and theme controls.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

$is_header_sticky = (bool) get_theme_mod('wordpress_demo_theme_is_header_sticky', false);
$is_header_auto_hide = (bool) get_theme_mod('wordpress_demo_theme_is_header_auto_hide', false);
$is_header_transparent = (bool) get_theme_mod('wordpress_demo_theme_is_header_transparent', false);
$is_header_button_visible = (bool) get_theme_mod('wordpress_demo_theme_is_header_button_visible', true);
$header_button_title = get_theme_mod('wordpress_demo_theme_header_button_title', __('Button', 'wordpress-demo-theme'));
$header_button_url = get_theme_mod('wordpress_demo_theme_header_button_url', '#');
$header_button_path = untrailingslashit((string) wp_parse_url($header_button_url, PHP_URL_PATH));
$login_path = untrailingslashit((string) wp_parse_url(home_url('/login/'), PHP_URL_PATH));

if(is_user_logged_in() && $header_button_path === $login_path) {
    $header_button_title = __('Dashboard', 'wordpress-demo-theme');
    $header_button_url = home_url('/dashboard/');
}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class('min-h-screen flex flex-col overflow-x-hidden bg-[var(--color-background)] text-[var(--color-text-primary)] antialiased'); ?>>
        <?php wp_body_open(); ?>

        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[var(--z-modal)] focus:rounded-lg focus:bg-[var(--color-surface)] focus:px-5 focus:py-3 focus:text-[var(--color-text-primary)]">
            <?php esc_html_e('Skip to content', 'wordpress-demo-theme'); ?>
        </a>

        <header id="header" data-sticky-header="true" class="fixed inset-x-0 top-0 z-[var(--z-sticky)] border-b border-[color-mix(in_srgb,var(--color-border)_70%,transparent)] bg-[color-mix(in_srgb,var(--color-surface)_80%,transparent)] backdrop-blur-md backdrop-saturate-150 transition-[top] duration-200 ease-out">
            <?php wordpress_demo_theme_website_header(); ?>
        </header>

        <div class="h-16 shrink-0" aria-hidden="true"></div>

        <div id="mobileMenuOverlay" class="fixed inset-0 z-[var(--z-overlay)] hidden lg:hidden" aria-hidden="true">
            <div id="mobileMenuBackdrop" class="absolute inset-0 bg-[var(--color-overlay)] opacity-0 transition-opacity duration-200"></div>

            <div id="mobileMenuPanel" class="absolute inset-y-0 left-0 z-10 flex w-80 max-w-[85vw] -translate-x-full flex-col overflow-hidden bg-[var(--color-card)] shadow-[var(--shadow-md)] transition-transform duration-200 ease-in-out" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Mobile Navigation', 'wordpress-demo-theme'); ?>">
                <div class="flex h-16 shrink-0 items-center justify-between border-b border-[var(--color-border)] px-5">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="header-site-branding inline-flex items-center [&_img]:!max-h-7 [&_img]:!max-w-28 [&_img]:!w-auto [&_img]:!object-contain" rel="home">
                        <?php wordpress_demo_theme_logo(); ?>
                    </a>

                    <button id="mobileMenuClose" type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-[var(--color-text-secondary)] hover:bg-[var(--color-surface-hover)]" aria-label="<?php esc_attr_e('Close Menu', 'wordpress-demo-theme'); ?>">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18"/>
                        </svg>
                    </button>
                </div>

                <nav class="min-h-0 flex-1 overflow-y-auto overscroll-y-contain" aria-label="<?php esc_attr_e('Mobile Navigation', 'wordpress-demo-theme'); ?>">
                    <?php wordpress_demo_theme_render_header_menu(true); ?>
                </nav>

                <?php if($is_header_button_visible) { ?>
                    <div class="shrink-0 border-t border-[var(--color-border)] bg-[var(--color-card)] p-4">
                        <a href="<?php echo esc_url($header_button_url); ?>" class="inline-flex h-10 w-full items-center justify-center rounded-full bg-[var(--color-primary)] px-4 text-sm text-[var(--color-primary-text)] transition hover:bg-[var(--color-primary-hover)]">
                            <?php echo esc_html($header_button_title); ?>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
