<?php
/**
 * Component: Blog Card
 *
 * Renders blog post cards using the
 * selected design and post information.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

$post_id = isset($post_id) ? absint($post_id) : get_the_ID();
$post = get_post($post_id);

if(!$post instanceof WP_Post || $post->post_type !== 'post') {
    return;
}

$available_designs = wordpress_demo_theme_get_available_designs('blog-card');
$selected_blog_card_design = get_theme_mod('wordpress_demo_theme_blog_card_design', 'design-1');

if(!isset($available_designs[$selected_blog_card_design])) {
    $selected_blog_card_design = 'design-1';
}

$post_title = get_the_title($post_id);
$post_url = get_permalink($post_id);
$post_excerpt = get_the_excerpt($post_id);
$post_date = get_the_date('F j, Y', $post_id);
$post_date_iso = get_the_date('c', $post_id);

$post_author_id = (int) $post->post_author;
$post_author = wordpress_demo_theme_get_author_name($post_id);
$post_author_url = get_author_posts_url($post_author_id);
$post_author_avatar = get_avatar_url($post_author_id, array('size' => 56));

$post_categories = get_the_category($post_id);
$post_has_thumbnail = has_post_thumbnail($post_id);
$post_interactions = wordpress_demo_theme_get_post_interactions($post_id);
$post_comment_count = (int) get_comments_number($post_id);
$post_reading_time = wordpress_demo_theme_reading_time($post_id);

switch($selected_blog_card_design) {
    case 'design-2':
        ?>
        <article id="post-<?php echo esc_attr($post_id); ?>" <?php post_class('blog-card group relative flex min-w-0 flex-col rounded-2xl bg-[var(--color-card)] shadow-[var(--shadow-sm)] transition-shadow hover:shadow-[var(--shadow-md)]', $post_id); ?>>
            <div class="relative aspect-[5/3] w-full shrink-0 overflow-hidden rounded-t-xl bg-[var(--color-border)]">
                <a href="<?php echo esc_url($post_url); ?>" class="relative block size-full" aria-label="<?php echo esc_attr($post_title); ?>">
                    <?php if($post_has_thumbnail) : ?>
                        <?php echo get_the_post_thumbnail($post_id, 'large', array(
                            'class' => 'size-full object-cover transition-transform duration-500 group-hover:scale-105',
                            'loading' => 'lazy'
                        )); ?>
                    <?php else : ?>
                        <div class="flex size-full items-center justify-center text-[var(--color-text-muted)]">
                            <svg class="size-12 opacity-40" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="3" y="3" width="18" height="18" rx="3"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 15-5-5L5 21"></path>
                            </svg>
                        </div>
                    <?php endif; ?>

                    <div class="absolute inset-0 bg-black/15 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                </a>

                <?php if(!empty($post_categories)) : ?>
                    <div class="absolute left-3 right-3 top-3 z-10 flex flex-wrap items-center gap-1.5">
                        <?php $category = $post_categories[0]; ?>

                        <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="inline-flex rounded-full bg-[var(--color-action-surface)] px-2.5 py-1 text-xs font-semibold text-[var(--color-text-secondary)] shadow-sm transition hover:bg-[var(--color-action-surface-hover)] hover:text-[var(--color-primary)]">
                            <?php echo esc_html($category->name); ?>
                        </a>

                        <?php if(count($post_categories) > 1) : ?>
                            <a href="<?php echo esc_url($post_url); ?>" class="inline-flex rounded-full bg-[var(--color-action-surface)] px-2.5 py-1 text-xs font-semibold text-[var(--color-text-secondary)] shadow-sm transition hover:bg-[var(--color-action-surface-hover)] hover:text-[var(--color-primary)]">
                                <?php echo esc_html(sprintf(
                                    __('+%d more', 'wordpress-demo-theme'),
                                    count($post_categories) - 1
                                )); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex grow flex-col gap-y-1.5 rounded-b-xl border border-t-0 border-[var(--color-border)] p-4">
                <div class="flex flex-wrap items-center text-xs text-[var(--color-text-muted)]">
                    <a href="<?php echo esc_url($post_author_url); ?>" class="flex min-w-0 items-center gap-2.5 hover:text-[var(--color-primary)]">
                        <span class="size-7 shrink-0 overflow-hidden rounded-full bg-[var(--color-surface-soft)]">
                            <?php echo get_avatar($post_author_id, 28, '', $post_author, array(
                                'class' => 'size-full object-cover',
                                'loading' => 'lazy'
                            )); ?>
                        </span>

                        <span class="truncate font-semibold text-[var(--color-text-primary)]">
                            <?php echo esc_html($post_author); ?>
                        </span>
                    </a>

                    <span class="mx-1.5 font-medium text-[var(--color-text-muted)]" aria-hidden="true">•</span>

                    <time datetime="<?php echo esc_attr($post_date_iso); ?>">
                        <?php echo esc_html($post_date); ?>
                    </time>
                </div>

                <h2 class="text-base font-semibold leading-snug text-[var(--color-text-heading)]">
                    <a href="<?php echo esc_url($post_url); ?>" class="line-clamp-2 transition-colors hover:text-[var(--color-link)]">
                        <?php echo esc_html($post_title); ?>
                    </a>
                </h2>

                <?php if($post_excerpt) : ?>
                    <p class="line-clamp-2 text-sm text-[var(--color-text-secondary)]">
                        <?php echo esc_html($post_excerpt); ?>
                    </p>
                <?php endif; ?>

                <div class="mt-auto flex flex-wrap items-center gap-2 pt-3" data-post-interactions data-post-id="<?php echo esc_attr($post_id); ?>" data-interaction-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-interaction-nonce="<?php echo esc_attr(wp_create_nonce('wordpress_demo_theme_post_interaction')); ?>" data-interaction-error="<?php esc_attr_e('Could not update this post. Please try again.', 'wordpress-demo-theme'); ?>">
                    <button type="button" class="inline-flex h-8 items-center rounded-full bg-[var(--color-action-surface)] px-2.5 text-xs text-[var(--color-text-secondary)] transition hover:bg-[var(--color-error-subtle)] hover:text-[var(--color-error)] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[var(--color-focus-ring)] disabled:cursor-wait disabled:opacity-60 aria-pressed:bg-[var(--color-error-subtle)] aria-pressed:text-[var(--color-error)]" data-post-interaction="like" aria-pressed="<?php echo $post_interactions['liked'] ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr($post_interactions['liked'] ? __('Unlike this post', 'wordpress-demo-theme') : __('Like this post', 'wordpress-demo-theme')); ?>" data-active-label="<?php esc_attr_e('Unlike this post', 'wordpress-demo-theme'); ?>" data-inactive-label="<?php esc_attr_e('Like this post', 'wordpress-demo-theme'); ?>">
                        <svg class="size-[18px] shrink-0" fill="<?php echo $post_interactions['liked'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M10.4107 19.9677C7.58942 17.858 2 13.0348 2 8.69444C2 5.82563 4.10526 3.5 7 3.5C8.5 3.5 10 4 12 6C14 4 15.5 3.5 17 3.5C19.8947 3.5 22 5.82563 22 8.69444C22 13.0348 16.4106 17.858 13.5893 19.9677C12.6399 20.6776 11.3601 20.6776 10.4107 19.9677Z"></path>
                        </svg>

                        <span class="ml-1.5 text-[14.5px]" data-post-like-count data-count="<?php echo esc_attr($post_interactions['like_count']); ?>">
                            <?php echo esc_html(wordpress_demo_theme_format_compact_number($post_interactions['like_count'])); ?>
                        </span>
                    </button>

                    <a href="<?php echo esc_url(get_comments_link($post_id)); ?>" class="inline-flex h-8 items-center rounded-full bg-[var(--color-action-surface)] px-2.5 text-xs text-[var(--color-text-secondary)] transition hover:bg-[var(--color-primary-subtle)] hover:text-[var(--color-primary-hover)] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[var(--color-focus-ring)]" aria-label="<?php echo esc_attr(sprintf(__('View comments on %s', 'wordpress-demo-theme'), $post_title)); ?>">
                        <svg class="size-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.5 4.5h11C19.4 4.5 20.5 5.6 20.5 7.5v7C20.5 16.4 19.4 17.5 17.5 17.5h-6L7.2 20.5C6.8 20.8 6.3 20.5 6.3 20v-2.5C4.5 17.4 3.5 16.3 3.5 14.5v-7C3.5 5.6 4.6 4.5 6.5 4.5Z"></path>
                            <path stroke-linecap="round" d="M8 9h8M8 13h5"></path>
                        </svg>

                        <span class="ml-1.5 text-[14.5px]">
                            <?php echo esc_html(wordpress_demo_theme_format_compact_number($post_comment_count)); ?>
                        </span>
                    </a>

                    <div class="ml-auto flex items-center gap-2 text-xs text-[var(--color-text-muted)]">
                        <span>
                            <?php echo esc_html(sprintf(
                                _n('%d min read', '%d min read', $post_reading_time, 'wordpress-demo-theme'),
                                $post_reading_time
                            )); ?>
                        </span>

                        <button type="button" class="flex size-8 items-center justify-center rounded-full bg-[var(--color-action-surface)] text-[var(--color-text-secondary)] transition hover:bg-[var(--color-primary-subtle)] hover:text-[var(--color-primary)] disabled:cursor-wait disabled:opacity-60 aria-pressed:bg-[var(--color-primary-subtle)] aria-pressed:text-[var(--color-primary)]" data-post-interaction="save" aria-pressed="<?php echo $post_interactions['saved'] ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr($post_interactions['saved'] ? __('Remove from saved posts', 'wordpress-demo-theme') : __('Save this post', 'wordpress-demo-theme')); ?>" data-active-label="<?php esc_attr_e('Remove from saved posts', 'wordpress-demo-theme'); ?>" data-inactive-label="<?php esc_attr_e('Save this post', 'wordpress-demo-theme'); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-[18px] shrink-0" viewBox="0 0 24 24" fill="<?php echo $post_interactions['saved'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 17.9808V9.70753C4 6.07416 4 4.25748 5.17157 3.12874C6.34315 2 8.22876 2 12 2C15.7712 2 17.6569 2 18.8284 3.12874C20 4.25748 20 6.07416 20 9.70753V17.9808C20 20.2867 20 21.4396 19.2272 21.8523C17.7305 22.6514 14.9232 19.9852 13.59 19.1824C12.8168 18.7168 12.4302 18.484 12 18.484C11.5698 18.484 11.1832 18.7168 10.41 19.1824C9.0768 19.9852 6.26947 22.6514 4.77285 21.8523C4 21.4396 4 20.2867 4 17.9808Z"></path>
                            </svg>
                        </button>
                    </div>

                    <span class="sr-only" data-post-interaction-status role="status" aria-live="polite"></span>
                </div>
            </div>
        </article>
        <?php
        break;

    case 'design-1':
    default:
        ?>
        <article id="post-<?php echo esc_attr($post_id); ?>" <?php post_class('blog-card theme-surface-transition group flex min-w-0 flex-col gap-4 rounded-2xl border border-[var(--color-border)] bg-[var(--color-card)] p-3.5 shadow-[var(--shadow-sm)] transition-all duration-300 hover:shadow-[var(--shadow-md)] md:flex-row md:items-stretch', $post_id); ?>>
            <div class="flex min-w-0 flex-1 flex-col">
                <?php if(!empty($post_categories)) : ?>
                    <div class="flex flex-wrap items-center gap-2">
                        <?php $category = $post_categories[0]; ?>

                        <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="inline-flex w-fit items-center rounded-full bg-[var(--color-primary-subtle)] px-2.5 py-1 text-[11px] font-semibold text-[var(--color-primary-on-subtle)] transition-colors duration-200 hover:bg-[var(--color-primary-subtle)] sm:text-xs">
                            <?php echo esc_html($category->name); ?>
                        </a>

                        <?php if(count($post_categories) > 1) : ?>
                            <a href="<?php echo esc_url($post_url); ?>" class="inline-flex w-fit items-center rounded-full bg-[var(--color-primary-subtle)] px-2.5 py-1 text-[11px] font-semibold text-[var(--color-primary-on-subtle)] transition-colors duration-200 hover:bg-[var(--color-primary-subtle)] sm:text-xs">
                                <?php echo esc_html(sprintf(__('+%d more', 'wordpress-demo-theme'), count($post_categories) - 1)); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <h2 class="mt-2 min-w-0 text-[15px] font-bold leading-snug text-[var(--color-text-primary)] sm:text-base">
                    <a href="<?php echo esc_url($post_url); ?>" class="line-clamp-2 break-words hover:text-[var(--color-primary)]">
                        <?php echo esc_html($post_title); ?>
                    </a>
                </h2>

                <?php if($post_excerpt) : ?>
                    <p class="mt-1.5 line-clamp-2 text-sm leading-5 text-[var(--color-text-secondary)]">
                        <?php echo esc_html($post_excerpt); ?>
                    </p>
                <?php endif; ?>

                <div class="mt-2.5 flex min-w-0 flex-wrap items-center gap-x-2 gap-y-2 text-[11px] text-[var(--color-text-muted)] sm:text-xs">
                    <a href="<?php echo esc_url($post_author_url); ?>" class="flex min-w-0 items-center gap-2 hover:text-[var(--color-primary)]">
                        <span class="size-7 shrink-0 overflow-hidden rounded-full bg-[var(--color-surface-soft)] ring-1 ring-[var(--color-border)] transition hover:ring-[var(--color-primary)]">
                            <?php echo get_avatar($post_author_id, 28, '', $post_author, array(
                                'class' => 'size-full object-cover',
                                'loading' => 'lazy'
                            )); ?>
                        </span>

                        <span class="max-w-[120px] truncate font-semibold text-[var(--color-text-primary)] transition hover:text-[var(--color-primary)]">
                            <?php echo esc_html($post_author); ?>
                        </span>
                    </a>

                    <span class="text-[var(--color-text-muted)]" aria-hidden="true">•</span>

                    <time class="shrink-0" datetime="<?php echo esc_attr($post_date_iso); ?>">
                        <?php echo esc_html($post_date); ?>
                    </time>
                </div>

                <div class="mt-auto flex flex-wrap items-center gap-2 pt-3" data-post-interactions data-post-id="<?php echo esc_attr($post_id); ?>" data-interaction-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-interaction-nonce="<?php echo esc_attr(wp_create_nonce('wordpress_demo_theme_post_interaction')); ?>" data-interaction-error="<?php esc_attr_e('Could not update this post. Please try again.', 'wordpress-demo-theme'); ?>">
                    <button type="button" class="inline-flex h-8 items-center rounded-full bg-[var(--color-action-surface)] px-2.5 text-xs text-[var(--color-text-secondary)] transition hover:bg-[var(--color-error-subtle)] hover:text-[var(--color-error)] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[var(--color-focus-ring)] disabled:cursor-wait disabled:opacity-60 aria-pressed:bg-[var(--color-error-subtle)] aria-pressed:text-[var(--color-error)]" data-post-interaction="like" aria-pressed="<?php echo $post_interactions['liked'] ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr($post_interactions['liked'] ? __('Unlike this post', 'wordpress-demo-theme') : __('Like this post', 'wordpress-demo-theme')); ?>" data-active-label="<?php esc_attr_e('Unlike this post', 'wordpress-demo-theme'); ?>" data-inactive-label="<?php esc_attr_e('Like this post', 'wordpress-demo-theme'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-[18px] shrink-0" viewBox="0 0 24 24" fill="<?php echo $post_interactions['liked'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M10.4107 19.9677C7.58942 17.858 2 13.0348 2 8.69444C2 5.82563 4.10526 3.5 7 3.5C8.5 3.5 10 4 12 6C14 4 15.5 3.5 17 3.5C19.8947 3.5 22 5.82563 22 8.69444C22 13.0348 16.4106 17.858 13.5893 19.9677C12.6399 20.6776 11.3601 20.6776 10.4107 19.9677Z"></path>
                        </svg>

                        <span class="ml-1.5 text-[14.5px]" data-post-like-count data-count="<?php echo esc_attr($post_interactions['like_count']); ?>">
                            <?php echo esc_html(wordpress_demo_theme_format_compact_number($post_interactions['like_count'])); ?>
                        </span>
                    </button>

                    <a href="<?php echo esc_url(get_comments_link($post_id)); ?>" class="inline-flex h-8 items-center rounded-full bg-[var(--color-action-surface)] px-2.5 text-xs text-[var(--color-text-secondary)] transition hover:bg-[var(--color-primary-subtle)] hover:text-[var(--color-primary-hover)] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[var(--color-focus-ring)]" aria-label="<?php echo esc_attr(sprintf(__('View comments on %s', 'wordpress-demo-theme'), $post_title)); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.5 4.5h11C19.4 4.5 20.5 5.6 20.5 7.5v7C20.5 16.4 19.4 17.5 17.5 17.5h-6L7.2 20.5C6.8 20.8 6.3 20.5 6.3 20v-2.5C4.5 17.4 3.5 16.3 3.5 14.5v-7C3.5 5.6 4.6 4.5 6.5 4.5Z"></path>
                            <path stroke-linecap="round" d="M8 9h8M8 13h5"></path>
                        </svg>

                        <span class="ml-1.5 text-[14.5px]">
                            <?php echo esc_html(wordpress_demo_theme_format_compact_number($post_comment_count)); ?>
                        </span>
                    </a>

                    <div class="ml-auto flex items-center gap-2 text-xs text-[var(--color-text-muted)]">
                        <span>
                            <?php echo esc_html(sprintf(
                                _n('%d min read', '%d min read', $post_reading_time, 'wordpress-demo-theme'),
                                $post_reading_time
                            )); ?>
                        </span>

                        <button type="button" class="flex size-8 items-center justify-center rounded-full bg-[var(--color-action-surface)] text-[var(--color-text-secondary)] transition hover:bg-[var(--color-primary-subtle)] hover:text-[var(--color-primary)] disabled:cursor-wait disabled:opacity-60 aria-pressed:bg-[var(--color-primary-subtle)] aria-pressed:text-[var(--color-primary)]" data-post-interaction="save" aria-pressed="<?php echo $post_interactions['saved'] ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr($post_interactions['saved'] ? __('Remove from saved posts', 'wordpress-demo-theme') : __('Save this post', 'wordpress-demo-theme')); ?>" data-active-label="<?php esc_attr_e('Remove from saved posts', 'wordpress-demo-theme'); ?>" data-inactive-label="<?php esc_attr_e('Save this post', 'wordpress-demo-theme'); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-[18px] shrink-0" viewBox="0 0 24 24" fill="<?php echo $post_interactions['saved'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M4 17.9808V9.70753C4 6.07416 4 4.25748 5.17157 3.12874C6.34315 2 8.22876 2 12 2C15.7712 2 17.6569 2 18.8284 3.12874C20 4.25748 20 6.07416 20 9.70753V17.9808C20 20.2867 20 21.4396 19.2272 21.8523C17.7305 22.6514 14.9232 19.9852 13.59 19.1824C12.8168 18.7168 12.4302 18.484 12 18.484C11.5698 18.484 11.1832 18.7168 10.41 19.1824C9.0768 19.9852 6.26947 22.6514 4.77285 21.8523C4 21.4396 4 20.2867 4 17.9808Z"></path>
                            </svg>
                        </button>
                    </div>

                    <span class="sr-only" data-post-interaction-status role="status" aria-live="polite"></span>
                </div>
            </div>

            <?php if($post_has_thumbnail) : ?>
                <a href="<?php echo esc_url($post_url); ?>" class="relative block h-48 w-full shrink-0 overflow-hidden rounded-xl bg-[var(--color-surface-soft)] md:h-auto md:w-[180px] xl:w-[190px]" aria-label="<?php echo esc_attr($post_title); ?>">
                    <?php echo get_the_post_thumbnail($post_id, 'large', array(
                        'class' => 'size-full object-cover transition-transform duration-700 ease-out group-hover:scale-105',
                        'loading' => 'lazy'
                    )); ?>

                    <div class="absolute inset-0 bg-black/0 transition-colors duration-300 group-hover:bg-black/10"></div>
                </a>
            <?php endif; ?>
        </article>
        <?php
        break;
}
