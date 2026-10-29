<?php
/**
 * Layout: Footer
 *
 * Displays site branding, footer navigation, copyright
 * information, back-to-top button, and search modal.
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
?>

<footer id="site-footer" class="border-t border-[var(--color-border)] bg-[var(--color-surface)]">
    <div class="mx-auto w-full max-w-full px-4 sm:max-w-[680px] sm:px-5 md:max-w-[820px] md:px-6 lg:max-w-[1100px] lg:px-8 xl:max-w-[1360px] xl:px-10 2xl:max-w-[1536px] 2xl:px-12 3xl:max-w-[1720px] 3xl:px-16 4xl:max-w-[2048px] 4xl:px-20">
        <?php if($is_custom_footer_visible) : ?>
            <?php $footer_section_count = count($custom_footer_sections); ?>
            <?php $footer_rendered_section_index = 0; ?>
            <?php
            $footer_grid_class = $footer_section_count === 4
                ? 'lg:grid-cols-[35fr_20fr_20fr_25fr]'
                : 'lg:grid-cols-12';
            ?>

            <div class="grid grid-cols-1 items-start gap-8 py-8 sm:grid-cols-2 md:py-10 <?php echo esc_attr($footer_grid_class); ?> lg:gap-10 lg:py-12 xl:gap-12">
                <?php foreach($custom_footer_sections as $footer_section) : ?>
                    <?php if(!is_array($footer_section)) { continue; } ?>
                    <?php $footer_blocks = isset($footer_section['blocks']) && is_array($footer_section['blocks']) ? $footer_section['blocks'] : array(); ?>
                    <?php

                    if($footer_section_count === 1) {
                        $footer_section_class = 'lg:col-span-12';
                    }
                    elseif($footer_section_count === 2) {
                        $footer_section_class = 'lg:col-span-6';
                    }
                    elseif($footer_section_count === 3) {
                        $footer_section_class = 'lg:col-span-4';
                    }
                    else {
                        $footer_section_class = '';
                    }

                    $footer_rendered_section_index++;
                    ?>

                    <section class="w-full min-w-0 break-words <?php echo esc_attr($footer_section_class); ?> [&>*+*]:mt-4">
                        <?php if(!empty($footer_section['name'])) : ?>
                            <h2 class="text-base font-semibold leading-6 text-[var(--color-text-primary)]"><?php echo esc_html($footer_section['name']); ?></h2>
                        <?php endif; ?>

                        <?php foreach($footer_blocks as $footer_block) : ?>
                            <?php
                            if(!is_array($footer_block)) {
                                continue;
                            }

                            $block_type = isset($footer_block['type']) ? sanitize_key($footer_block['type']) : '';
                            ?>

                            <?php if($block_type === 'image') : ?>
                                <?php
                                $use_site_logo = !empty($footer_block['use_site_logo']);
                                $image_id = absint($footer_block['image_id'] ?? 0);
                                ?>
                                <?php if($use_site_logo) : ?>
                                    <div class="[&_img]:h-9 [&_img]:w-auto [&_img]:max-w-[180px] [&_img]:object-contain">
                                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                            <?php wordpress_demo_theme_logo(); ?>
                                        </a>
                                    </div>
                                <?php elseif($image_id && wp_attachment_is_image($image_id)) : ?>
                                    <div class="[&_img]:h-9 [&_img]:w-auto [&_img]:max-w-[180px] [&_img]:object-contain">
                                        <?php echo wp_get_attachment_image($image_id, 'full', false, array('loading' => 'lazy')); ?>
                                    </div>
                                <?php endif; ?>
                            <?php elseif($block_type === 'heading' && !empty($footer_block['text'])) : ?>
                                <h2 class="text-base font-semibold leading-6 text-[var(--color-text-primary)]"><?php echo esc_html($footer_block['text']); ?></h2>
                            <?php elseif($block_type === 'description' && !empty($footer_block['text'])) : ?>
                                <p class="max-w-md text-[15px] leading-6 text-[var(--color-text-secondary)]"><?php echo nl2br(esc_html($footer_block['text'])); ?></p>
                            <?php elseif($block_type === 'social_links' && !empty($footer_block['items']) && is_array($footer_block['items'])) : ?>
                                <div class="flex flex-wrap items-center gap-4 [&_a]:inline-flex [&_a]:text-[var(--color-text-muted)] [&_a]:transition-colors [&_a:hover]:text-[var(--color-primary)] [&_img]:size-5 [&_img]:object-contain">
                                    <?php foreach($footer_block['items'] as $social_link) : ?>
                                        <?php
                                        $social_image_id = absint($social_link['image_id'] ?? 0);
                                        $social_url = isset($social_link['url']) ? esc_url($social_link['url']) : '';

                                        if(!$social_image_id || !$social_url || !wp_attachment_is_image($social_image_id)) {
                                            continue;
                                        }
                                        ?>
                                        <a href="<?php echo $social_url; ?>" target="_blank" rel="noopener noreferrer">
                                            <?php echo wp_get_attachment_image($social_image_id, 'thumbnail', false, array('loading' => 'lazy')); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php elseif($block_type === 'navigation_menu' && !empty($footer_block['menu_id'])) : ?>
                                <nav class="[&_a]:text-[15px] [&_a]:text-[var(--color-text-secondary)] [&_a]:transition-colors [&_a:hover]:text-[var(--color-primary)] [&_ul]:m-0 [&_ul]:flex [&_ul]:list-none [&_ul]:flex-col [&_ul]:gap-2 [&_ul]:p-0" aria-label="<?php esc_attr_e('Footer section navigation', 'wordpress-demo-theme'); ?>">
                                    <?php
                                    wp_nav_menu(array(
                                        'theme_location' => 'footer',
                                        'menu' => absint($footer_block['menu_id']),
                                        'container' => false,
                                        'fallback_cb' => false,
                                        'depth' => 1
                                    ));
                                    ?>
                                </nav>
                            <?php elseif($block_type === 'contact_details' && !empty($footer_block['items']) && is_array($footer_block['items'])) : ?>
                                <div class="flex flex-col gap-3 text-[15px]">
                                    <?php foreach($footer_block['items'] as $contact_item) : ?>
                                        <?php
                                        $contact_image_id = absint($contact_item['image_id'] ?? 0);
                                        $contact_label = isset($contact_item['label']) ? $contact_item['label'] : '';

                                        if(!$contact_image_id && $contact_label === '') {
                                            continue;
                                        }
                                        ?>
                                        
                                        <div class="flex items-center gap-3 text-[var(--color-text-secondary)] [&_img]:size-4 [&_img]:shrink-0 [&_img]:object-contain [&_strong]:font-normal">
                                            <?php if($contact_image_id && wp_attachment_is_image($contact_image_id)) : ?>
                                                <?php echo wp_get_attachment_image($contact_image_id, 'thumbnail', false, array('loading' => 'lazy')); ?>
                                            <?php endif; ?>
                                            <?php if($contact_label !== '') : ?>
                                                <strong><?php echo esc_html($contact_label); ?></strong>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php elseif($block_type === 'custom_html' && !empty($footer_block['html'])) : ?>
                                <div class="text-[15px] leading-7 text-[var(--color-text-secondary)] [&_a]:text-[var(--color-primary)] [&_a]:underline [&_img]:h-auto [&_img]:max-w-full [&_p+p]:mt-3">
                                    <?php echo !empty($footer_block['is_unfiltered_html']) ? $footer_block['html'] : wp_kses_post($footer_block['html']); ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="flex py-6">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center [&_img]:!max-h-9 [&_img]:!max-w-40 [&_img]:!w-auto [&_img]:!object-contain" rel="home">
                    <?php wordpress_demo_theme_logo(); ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if($is_footer_copyright_visible || has_nav_menu('footer')) : ?>
            <div class="flex flex-col items-center justify-between gap-4 border-t border-[var(--color-border)] py-4 text-center md:flex-row md:text-left">
                <?php if($is_footer_copyright_visible) : ?>
                    <p class="text-sm leading-6 text-[var(--color-text-muted)]">
                        <?php echo wordpress_demo_theme_get_copyright(); ?>
                    </p>
                <?php endif; ?>

                <?php if(has_nav_menu('footer')) : ?>
                    <nav aria-label="<?php esc_attr_e('Footer Navigation', 'wordpress-demo-theme'); ?>">
                        <?php
                        wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'menu_class' => implode(' ', array(
                            'flex flex-wrap items-center justify-center',
                            'gap-x-6 gap-y-3 text-[14.5px] font-medium',
                            'md:justify-end',
                            '[&_a]:text-[var(--color-text-secondary)]',
                            '[&_a]:transition-colors',
                            '[&_a]:duration-200',
                            '[&_a:hover]:!text-[var(--color-primary)]',
                            '[&_a:focus-visible]:!text-[var(--color-primary)]',
                            '[&_.current-menu-item>a]:!text-[var(--color-primary)]',
                            '[&_.current-menu-item>a]:!font-semibold',
                            '[&_.current_page_item>a]:!text-[var(--color-primary)]',
                            '[&_.current_page_item>a]:!font-semibold',
                            '[&_.current-menu-ancestor>a]:!text-[var(--color-primary)]',
                            '[&_.current-menu-ancestor>a]:!font-semibold',
                            '[&_.current_page_ancestor>a]:!text-[var(--color-primary)]',
                            '[&_.current_page_ancestor>a]:!font-semibold',
                        )),
                        'container' => false,
                        'depth' => 1,
                        'fallback_cb' => false
                        ));
                        ?>
                    </nav>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</footer>

<?php if($is_scroll_to_top_button_visible) : ?>
    <button type="button" class="pointer-events-none fixed bottom-5 right-5 z-[var(--z-sticky)] inline-flex h-11 w-11 translate-y-3 items-center justify-center rounded-full bg-[var(--color-primary)] text-[var(--color-primary-text)] opacity-0 shadow-[var(--shadow-md)] transition hover:bg-[var(--color-primary-hover)]" aria-label="<?php esc_attr_e('Back to top', 'wordpress-demo-theme'); ?>" data-back-to-top>
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0-6 6m6-6 6 6"/>
        </svg>
    </button>
<?php endif; ?>

<div id="search-modal" class="pointer-events-none fixed inset-0 z-[var(--z-modal)] flex items-center justify-center bg-[var(--color-overlay)] px-4 py-6 opacity-0 backdrop-blur-sm transition-opacity duration-200" aria-hidden="true" role="dialog" aria-modal="true">
    <div id="search-modal-panel" class="mx-auto w-full max-w-2xl translate-y-6 scale-[0.98] divide-y divide-[var(--color-border)] overflow-hidden rounded-2xl bg-[var(--color-surface)] opacity-0 shadow-[var(--shadow-lg)] ring-1 ring-[var(--color-border)] transition-all duration-200">
        <form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="relative">
            <svg class="pointer-events-none absolute start-4 top-1/2 size-5 -translate-y-1/2 text-[var(--color-text-muted)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z"></path>
            </svg>

            <input id="search-modal-input" type="search" name="s" required value="<?php echo esc_attr(get_search_query()); ?>" autocomplete="off" placeholder="<?php esc_attr_e('Type to search...', 'wordpress-demo-theme'); ?>" class="h-12 w-full border-0 bg-transparent ps-12 pe-12 text-[var(--color-text-primary)] outline-none placeholder:text-[var(--color-text-muted)] focus:ring-0 sm:text-sm">

            <button id="search-modal-close" type="button" class="absolute end-3 top-1/2 z-10 flex -translate-y-1/2 items-center justify-center text-xs text-[var(--color-text-muted)] transition hover:text-[var(--color-text-primary)] focus:outline-none sm:end-4" aria-label="<?php esc_attr_e('Close search', 'wordpress-demo-theme'); ?>">
                <svg class="size-5 sm:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75 17.25 17.25M17.25 6.75 6.75 17.25"></path>
                </svg>
                <span class="hidden sm:block"><kbd class="font-sans text-[var(--color-text-muted)]"><?php esc_html_e('Esc', 'wordpress-demo-theme'); ?></kbd></span>
            </button>
        </form>

        <ul id="search-modal-results" class="hidden-scrollbar max-h-[70vh] scroll-py-2 divide-y divide-[var(--color-border)] overflow-y-auto" data-search-results data-search-endpoint="<?php echo esc_url(rest_url('wp/v2/search')); ?>" data-search-url="<?php echo esc_url(home_url('/')); ?>" data-suggestions-title="<?php esc_attr_e('Suggestions', 'wordpress-demo-theme'); ?>" data-loading-text="<?php esc_attr_e('Searching...', 'wordpress-demo-theme'); ?>" data-empty-text="<?php esc_attr_e('No suggestions found.', 'wordpress-demo-theme'); ?>">
            <li class="p-2">
                <h2 class="mt-4 mb-2 px-3 text-xs font-medium text-[var(--color-text-muted)]"><?php esc_html_e('Recommended searches', 'wordpress-demo-theme'); ?></h2>
                <ul class="text-sm text-[var(--color-text-secondary)]">
                    <?php foreach(array(__('Design', 'wordpress-demo-theme'), __('Marketing', 'wordpress-demo-theme'), __('Ideas', 'wordpress-demo-theme')) as $search_term) : ?>
                        <li>
                            <a href="<?php echo esc_url(add_query_arg('s', rawurlencode($search_term), home_url('/'))); ?>" class="flex cursor-pointer items-center rounded-md px-3 py-2 transition hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-text-primary)]">
                                <svg class="size-5 flex-none text-[var(--color-text-muted)]" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z"></path>
                                </svg>
                                <span class="ms-3 flex-auto truncate"><?php echo esc_html($search_term); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        </ul>
    </div>
</div>

<?php wp_footer(); ?>

</body>
</html>
