<?php
/**
 * Component: Website Header
 *
 * Renders the active website header design.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

$available_designs = wordpress_demo_theme_get_available_designs('header');
$selected_header_design = get_theme_mod('wordpress_demo_theme_header_design', 'design-1');
$is_header_button_visible = (bool) get_theme_mod('wordpress_demo_theme_is_header_button_visible', true);
$header_button_title = get_theme_mod('wordpress_demo_theme_header_button_title', __('Button', 'wordpress-demo-theme'));
$header_button_url = get_theme_mod('wordpress_demo_theme_header_button_url', '#');
$header_button_path = untrailingslashit((string) wp_parse_url($header_button_url, PHP_URL_PATH));
$login_path = untrailingslashit((string) wp_parse_url(home_url('/login/'), PHP_URL_PATH));

if(is_user_logged_in() && $header_button_path === $login_path) {
    $header_button_title = __('Dashboard', 'wordpress-demo-theme');
    $header_button_url = home_url('/dashboard/');
}
$is_header_search_icon_visible = (bool) get_theme_mod('wordpress_demo_theme_is_header_search_icon_visible', true);
$is_header_dark_mode_toggle_visible = (bool) get_theme_mod('wordpress_demo_theme_is_header_dark_mode_toggle_visible', true);

if(!function_exists('wordpress_demo_theme_render_header_menu')) {
    function wordpress_demo_theme_render_header_menu($mobile = false) {
        $menu_locations = get_nav_menu_locations();

        if(empty($menu_locations['primary'])) {
            return;
        }

        $menu_items = wp_get_nav_menu_items($menu_locations['primary']);

        if(empty($menu_items) || is_wp_error($menu_items)) {
            return;
        }

        $menu_tree = array();
        $menu_lookup = array();
        $active_items = array();
        $current_url = home_url(wp_parse_url(add_query_arg(array()), PHP_URL_PATH) ?: '/');
        $current_post_id = get_queried_object_id();

        foreach($menu_items as $menu_item) {
            $menu_id = absint($menu_item->ID);
            $parent_id = absint($menu_item->menu_item_parent);

            $menu_lookup[$menu_id] = $menu_item;
            $menu_tree[$parent_id][] = $menu_item;
        }

        if(empty($menu_tree[0])) {
            return;
        }

        foreach($menu_items as $menu_item) {
            $menu_id = absint($menu_item->ID);
            $classes = (array) $menu_item->classes;

            $active_classes = array(
                'current-menu-item',
                'current_page_item',
                'current-menu-parent',
                'current-menu-ancestor',
                'current_page_parent',
                'current_page_ancestor',
                'current-post-ancestor',
                'current-category-ancestor',
                'current-category-parent'
            );

            $is_active = !empty(array_intersect($classes, $active_classes));

            if(!$is_active && $current_post_id && $menu_item->object_id == $current_post_id) {
                if(in_array($menu_item->type, array('post_type', 'taxonomy'), true)) {
                    $is_active = true;
                }
            }

            if(!$is_active && $menu_item->type === 'custom' && !empty($menu_item->url)) {
                $menu_url = untrailingslashit($menu_item->url);
                $page_url = untrailingslashit($current_url);

                if($menu_url === $page_url) {
                    $is_active = true;
                }
            }

            if(!$is_active) {
                continue;
            }

            $active_items[$menu_id] = true;
            $parent_id = absint($menu_item->menu_item_parent);
            $visited = array();

            while($parent_id && !isset($visited[$parent_id])) {
                $visited[$parent_id] = true;
                $active_items[$parent_id] = true;

                $parent_id = isset($menu_lookup[$parent_id])
                    ? absint($menu_lookup[$parent_id]->menu_item_parent)
                    : 0;
            }
        }

        $render_menu = function($parent_id = 0) use (&$render_menu, $menu_tree, $active_items, $mobile, $current_url, $current_post_id) {
            if(empty($menu_tree[$parent_id])) {
                return;
            }

            $is_submenu = $parent_id !== 0;

            foreach($menu_tree[$parent_id] as $menu_item) {
                $menu_id = absint($menu_item->ID);
                $has_children = !empty($menu_tree[$menu_id]);
                $classes = (array) $menu_item->classes;

                $is_current = in_array('current-menu-item', $classes, true)
                    || in_array('current_page_item', $classes, true);

                if(!$is_current && $current_post_id && $menu_item->object_id == $current_post_id) {
                    if(in_array($menu_item->type, array('post_type', 'taxonomy'), true)) {
                        $is_current = true;
                    }
                }

                if(!$is_current && $menu_item->type === 'custom' && !empty($menu_item->url)) {
                    $is_current = untrailingslashit($menu_item->url) === untrailingslashit($current_url);
                }

                $is_active = !empty($active_items[$menu_id]);

                $active_class = $is_active ? ' !text-[var(--color-primary)] !font-semibold' : '';
                $active_background = $is_active ? ' bg-[var(--color-surface-hover)]' : '';
                $aria_current = $is_current ? ' aria-current="page"' : '';

                $title = esc_html($menu_item->title);
                $url = esc_url($menu_item->url);
                $target_value = !empty($menu_item->target) ? $menu_item->target : '';
                $target = $target_value ? ' target="' . esc_attr($target_value) . '"' : '';
                $rel = trim((string) $menu_item->xfn . ($target_value === '_blank' ? ' noopener noreferrer' : ''));
                $rel_attribute = $rel ? ' rel="' . esc_attr($rel) . '"' : '';

                if($mobile) {
                    if($has_children) {
                        if($is_submenu) {
                            echo '<details class="group/sub border-b border-[var(--color-divider)] last:border-0"' . ($is_active ? ' open' : '') . '>';
                            echo '<summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 text-sm font-medium text-[var(--color-text-secondary)] hover:text-[var(--color-primary)] [&::-webkit-details-marker]:hidden' . esc_attr($active_class . $active_background) . '">';
                            echo '<span class="min-w-0 break-words">' . $title . '</span>';
                            echo '<svg class="h-4 w-4 shrink-0 transition-transform group-open/sub:rotate-180" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>';
                            echo '</summary>';
                            echo '<div class="ml-4 border-l border-[var(--color-border)] bg-[var(--color-surface-soft)]">';
                            $render_menu($menu_id);
                            echo '</div></details>';
                        }
                        else {
                            echo '<details class="group/menu border-b border-[var(--color-divider)]"' . ($is_active ? ' open' : '') . '>';
                            echo '<summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-5 py-4 text-sm font-medium text-[var(--color-text-primary)] transition-colors hover:bg-[var(--color-surface-hover)] [&::-webkit-details-marker]:hidden' . esc_attr($active_class . $active_background) . '">';
                            echo '<span>' . $title . '</span>';
                            echo '<svg class="h-4 w-4 shrink-0 transition-transform group-open/menu:rotate-180" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>';
                            echo '</summary>';
                            echo '<div class="bg-[var(--color-surface-soft)]">';
                            $render_menu($menu_id);
                            echo '</div></details>';
                        }
                    }
                    else {
                        if($is_submenu) {
                            echo '<a href="' . $url . '" class="block break-words px-4 py-3 text-sm font-medium text-[var(--color-text-secondary)] transition-colors hover:text-[var(--color-primary)]' . esc_attr($active_class . $active_background) . '"' . $target . $rel_attribute . $aria_current . '>' . $title . '</a>';
                        }
                        else {
                            echo '<a href="' . $url . '" class="block border-b border-[var(--color-divider)] px-5 py-4 text-sm font-medium text-[var(--color-text-primary)] transition-colors hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-primary)]' . esc_attr($active_class . $active_background) . '"' . $target . $rel_attribute . $aria_current . '>' . $title . '</a>';
                        }
                    }

                    continue;
                }

                if($has_children) {
                    if($is_submenu) {
                        echo '<div class="relative [&:hover>div]:visible [&:hover>div]:pointer-events-auto [&:hover>div]:opacity-100 [&:focus-within>div]:visible [&:focus-within>div]:pointer-events-auto [&:focus-within>div]:opacity-100" data-desktop-dropdown-item>';
                        echo '<button type="button" class="flex w-full items-center justify-between gap-4 rounded-lg px-3 py-2.5 text-left text-sm font-medium text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-primary)]' . esc_attr($active_class . $active_background) . '">';
                        echo '<span>' . $title . '</span>';
                        echo '<svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/></svg>';
                        echo '</button>';
                        echo '<div class="invisible pointer-events-none absolute left-full top-0 z-50 min-w-56 pl-2 opacity-0 transition-opacity duration-150" data-desktop-submenu data-submenu-level="nested">';
                        echo '<div class="rounded-xl border border-[var(--color-border)] bg-[var(--color-card)] p-2 shadow-[var(--shadow-md)]">';
                        $render_menu($menu_id);
                        echo '</div></div></div>';
                    }
                    else {
                        echo '<div class="relative [&:hover>div]:visible [&:hover>div]:pointer-events-auto [&:hover>div]:opacity-100 [&:focus-within>div]:visible [&:focus-within>div]:pointer-events-auto [&:focus-within>div]:opacity-100" data-desktop-dropdown>';
                        echo '<button type="button" class="flex items-center gap-1.5 transition-colors hover:text-[var(--color-primary)]' . esc_attr($active_class) . '">';
                        echo $title;
                        echo '<svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>';
                        echo '</button>';
                        echo '<div class="invisible pointer-events-none absolute left-1/2 top-full z-50 min-w-56 -translate-x-1/2 pt-5 opacity-0 transition-opacity duration-150" data-desktop-submenu data-submenu-level="top">';
                        echo '<div class="rounded-xl border border-[var(--color-border)] bg-[var(--color-card)] p-2 shadow-[var(--shadow-md)]">';
                        $render_menu($menu_id);
                        echo '</div></div></div>';
                    }
                }
                else {
                    if($is_submenu) {
                        echo '<a href="' . $url . '" class="block rounded-lg px-3 py-2 text-sm font-medium text-[var(--color-text-secondary)] transition-colors hover:text-[var(--color-primary)]' . esc_attr($active_class . $active_background) . '"' . $target . $rel_attribute . $aria_current . '>' . $title . '</a>';
                    }
                    else {
                        echo '<a href="' . $url . '" class="transition-colors hover:text-[var(--color-primary)]' . esc_attr($active_class) . '"' . $target . $rel_attribute . $aria_current . '>' . $title . '</a>';
                    }
                }
            }
        };

        $render_menu();
    }
}

switch($selected_header_design) {
    case 'design-2':
        ?>
        <div class="mx-auto flex h-16 w-full items-center justify-between gap-2 max-w-full px-4 sm:max-w-[680px] sm:px-5 md:max-w-[820px] md:px-6 lg:max-w-[1100px] lg:px-8 xl:max-w-[1360px] xl:px-10 2xl:max-w-[1536px] 2xl:px-12 3xl:max-w-[1720px] 3xl:px-16 4xl:max-w-[2048px] 4xl:px-20">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="header-site-branding inline-flex shrink-0 items-center gap-2 site-branding [&_img]:!max-h-9 [&_img]:!max-w-40 [&_img]:!w-auto [&_img]:!object-contain" rel="home">
                <?php wordpress_demo_theme_logo(); ?>
            </a>

            <nav class="mx-auto hidden min-w-0 items-center justify-center gap-6 text-[15px] font-medium text-[var(--color-text-secondary)] lg:flex primary-navigation" aria-label="<?php esc_attr_e('Primary Navigation', 'wordpress-demo-theme'); ?>">
                <?php wordpress_demo_theme_render_header_menu(); ?>
            </nav>

            <div class="flex shrink-0 items-center gap-0 sm:gap-1">
                <button type="button" data-search-open aria-controls="search-modal" aria-expanded="false" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-primary)] sm:h-10 sm:w-10" aria-label="<?php esc_attr_e('Open search', 'wordpress-demo-theme'); ?>">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
                    </svg>
                </button>

                <button type="button" data-theme-toggle class="inline-flex h-9 w-9 items-center justify-center rounded-full text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-primary)] sm:h-10 sm:w-10" aria-label="<?php esc_attr_e('Toggle theme', 'wordpress-demo-theme'); ?>">
                    <svg data-theme-moon class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/>
                    </svg>
                    <svg data-theme-sun class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="4"></circle>
                        <path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"></path>
                    </svg>
                </button>

                <a href="#" class="hidden rounded-full bg-[var(--color-primary)] px-4 py-2 text-sm font-medium text-[var(--color-primary-text)] transition-colors hover:bg-[var(--color-primary-hover)] sm:inline-flex">
                    Login
                </a>

                <button type="button" id="mobileMenuToggle" aria-controls="mobileMenuOverlay" aria-expanded="false" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-primary)] sm:h-10 sm:w-10 lg:hidden" aria-label="<?php esc_attr_e('Open menu', 'wordpress-demo-theme'); ?>">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>
            </div>
        </div>
        <?php
        break;

    case 'design-3':
        ?>
        <div class="mx-auto flex h-16 w-full items-center justify-between gap-2 max-w-full px-4 sm:max-w-[680px] sm:px-5 md:max-w-[820px] md:px-6 lg:max-w-[1100px] lg:px-8 xl:max-w-[1360px] xl:px-10 2xl:max-w-[1536px] 2xl:px-12 3xl:max-w-[1720px] 3xl:px-16 4xl:max-w-[2048px] 4xl:px-20">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="header-site-branding inline-flex shrink-0 items-center gap-2 site-branding [&_img]:!max-h-9 [&_img]:!max-w-40 [&_img]:!w-auto [&_img]:!object-contain" rel="home">
                <?php wordpress_demo_theme_logo(); ?>
            </a>

            <nav class="mr-auto ml-4 hidden min-w-0 items-center justify-start gap-6 text-[15px] font-medium text-[var(--color-text-secondary)] lg:flex primary-navigation" aria-label="<?php esc_attr_e('Primary Navigation', 'wordpress-demo-theme'); ?>">
                <?php wordpress_demo_theme_render_header_menu(); ?>
            </nav>

            <div class="flex shrink-0 items-center gap-0 sm:gap-1">
                <button type="button" data-search-open aria-controls="search-modal" aria-expanded="false" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-primary)] sm:h-10 sm:w-10" aria-label="<?php esc_attr_e('Open search', 'wordpress-demo-theme'); ?>">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
                    </svg>
                </button>

                <button type="button" data-theme-toggle class="inline-flex h-9 w-9 items-center justify-center rounded-full text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-primary)] sm:h-10 sm:w-10" aria-label="<?php esc_attr_e('Toggle theme', 'wordpress-demo-theme'); ?>">
                    <svg data-theme-moon class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/>
                    </svg>
                    <svg data-theme-sun class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="4"></circle>
                        <path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"></path>
                    </svg>
                </button>

                <a href="#" class="hidden rounded-full bg-[var(--color-primary)] px-4 py-2 text-sm font-medium text-[var(--color-primary-text)] transition-colors hover:bg-[var(--color-primary-hover)] sm:inline-flex">
                    Login
                </a>

                <button type="button" id="mobileMenuToggle" aria-controls="mobileMenuOverlay" aria-expanded="false" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-primary)] sm:h-10 sm:w-10 lg:hidden" aria-label="<?php esc_attr_e('Open menu', 'wordpress-demo-theme'); ?>">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>
            </div>
        </div>
        <?php
        break;

    case 'design-1':
    default:
        ?>
        <div class="mx-auto flex h-16 w-full items-center justify-between gap-2 max-w-full px-4 sm:max-w-[680px] sm:px-5 md:max-w-[820px] md:px-6 lg:max-w-[1100px] lg:px-8 xl:max-w-[1360px] xl:px-10 2xl:max-w-[1536px] 2xl:px-12 3xl:max-w-[1720px] 3xl:px-16 4xl:max-w-[2048px] 4xl:px-20">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="header-site-branding inline-flex shrink-0 items-center gap-2 site-branding [&_img]:!max-h-9 [&_img]:!max-w-40 [&_img]:!w-auto [&_img]:!object-contain" rel="home">
                <?php wordpress_demo_theme_logo(); ?>
            </a>

            <nav class="ml-auto hidden min-w-0 items-center justify-end gap-6 text-[15px] font-medium text-[var(--color-text-secondary)] lg:flex primary-navigation" aria-label="<?php esc_attr_e('Primary Navigation', 'wordpress-demo-theme'); ?>">
                <?php wordpress_demo_theme_render_header_menu(); ?>
            </nav>

            <div class="flex shrink-0 items-center gap-0 sm:gap-1">
                <?php if($is_header_search_icon_visible) { ?>
                    <button type="button" data-search-open aria-controls="search-modal" aria-expanded="false" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-primary)] sm:h-10 sm:w-10" aria-label="<?php esc_attr_e('Open search', 'wordpress-demo-theme'); ?>">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
                        </svg>
                    </button>
                <?php } ?>

                <?php if($is_header_dark_mode_toggle_visible) { ?>
                    <button type="button" data-theme-toggle class="inline-flex h-9 w-9 items-center justify-center rounded-full text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-primary)] sm:h-10 sm:w-10" aria-label="<?php esc_attr_e('Toggle theme', 'wordpress-demo-theme'); ?>">
                        <svg data-theme-moon class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/>
                        </svg>
                        <svg data-theme-sun class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="4"></circle>
                            <path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"></path>
                        </svg>
                    </button>
                <?php } ?>

                <?php if($is_header_button_visible) { ?>
                    <a href="<?php echo esc_url($header_button_url); ?>" class="hidden rounded-full bg-[var(--color-primary)] px-4 py-2 text-sm font-medium text-[var(--color-primary-text)] transition-colors hover:bg-[var(--color-primary-hover)] sm:inline-flex">
                        <?php echo esc_html($header_button_title); ?>
                    </a>
                <?php } ?>

                <button type="button" id="mobileMenuToggle" aria-controls="mobileMenuOverlay" aria-expanded="false" class="inline-flex h-9 w-9 items-center justify-center rounded-full text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-primary)] sm:h-10 sm:w-10 lg:hidden" aria-label="<?php esc_attr_e('Open menu', 'wordpress-demo-theme'); ?>">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>
            </div>
        </div>
        <?php
        break;
}
