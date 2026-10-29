<?php
/**
 * Page: Single Post
 *
 * Displays post content, metadata, navigation,
 * sidebar, table of contents, and comments.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

get_header();

$popular_categories = function_exists('wordpress_demo_theme_get_popular_categories')
    ? wordpress_demo_theme_get_popular_categories(8)
    : get_categories(array(
        'orderby' => 'count',
        'order' => 'DESC',
        'number' => 8,
        'hide_empty' => true
    ));

$post_id = get_the_ID();
$post_url = get_permalink($post_id);
$post_categories = wordpress_demo_theme_get_post_categories($post_id);
$author_id = (int) get_post_field('post_author', $post_id);
$author_url = get_author_posts_url($author_id);
$author_name = wordpress_demo_theme_get_author_name($post_id);
$author_profile_picture = wordpress_demo_theme_get_author_profile_picture($post_id);
$post_interactions = wordpress_demo_theme_get_post_interactions($post_id);
?>

<main id="main-content" class="flex-1 py-6 sm:py-8 lg:py-10">
    <div class="container">
        <div class="mx-auto max-w-7xl">
            <?php while(have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('min-w-0'); ?>>
                    <?php
                    $posts_page_id = (int) get_option('page_for_posts');
                    $breadcrumb_title = get_the_title();

                    wordpress_demo_theme_breadcrumb(array(
                        'items' => array(
                            array(
                                'title' => __('Home', 'wordpress-demo-theme'),
                                'url' => home_url('/')
                            ),
                            array(
                                'title' => $posts_page_id ? get_the_title($posts_page_id) : __('Blog', 'wordpress-demo-theme'),
                                'url' => $posts_page_id ? get_permalink($posts_page_id) : home_url('/')
                            ),
                            array(
                                'title' => $breadcrumb_title !== '' ? $breadcrumb_title : __('Untitled', 'wordpress-demo-theme'),
                                'url' => ''
                            )
                        )
                    ));
                    ?>

                    <header class="mb-6 md:mb-8">
                        <?php if(!empty($post_categories)) : ?>
                            <div class="flex flex-wrap items-center gap-2 text-xs text-[var(--color-text-muted)] sm:text-sm">
                                <?php foreach($post_categories as $post_category) : ?>
                                    <a href="<?php echo esc_url(get_category_link($post_category->term_id)); ?>" class="inline-flex max-w-full items-center break-words rounded-full bg-[var(--color-primary-subtle)] px-3 py-1.5 font-semibold text-[var(--color-primary-on-subtle)] transition-colors hover:bg-[var(--color-surface-hover)]">
                                        <?php echo esc_html($post_category->name); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <h1 class="mt-4 max-w-7xl text-3xl font-bold leading-tight tracking-tight text-[var(--color-text-primary)] sm:text-4xl sm:leading-tight lg:text-[42px] lg:leading-[1.15] 2xl:text-[45px]">
                            <?php the_title(); ?>
                        </h1>

                        <?php if(has_excerpt()) : ?>
                            <p class="mt-4 max-w-4xl text-base leading-7 text-[var(--color-text-secondary)] sm:text-lg sm:leading-8">
                                <?php echo esc_html(get_the_excerpt()); ?>
                            </p>
                        <?php endif; ?>

                        <div class="mt-6 flex flex-col items-stretch justify-between gap-4 border-y border-[var(--color-divider)] py-4 sm:flex-row sm:items-center sm:gap-4">
                            <div class="flex min-w-0 items-center gap-3 sm:flex-1">
                                <a href="<?php echo esc_url($author_url); ?>" class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[var(--color-surface-soft)] ring-1 ring-[var(--color-border)] sm:size-11 [&_img]:!m-0 [&_img]:!block [&_img]:!h-full [&_img]:!w-full [&_img]:!max-w-none [&_img]:!rounded-full [&_img]:!object-cover">
                                    <img src="<?php echo esc_url($author_profile_picture); ?>" alt="<?php echo esc_attr($author_name); ?>" width="88" height="88" loading="lazy" class="size-full object-cover">
                                </a>

                                <div class="min-w-0 flex-1">
                                    <a href="<?php echo esc_url($author_url); ?>" class="block w-fit whitespace-normal break-words text-sm font-semibold text-[var(--color-text-primary)] transition hover:text-[var(--color-primary)]">
                                        <?php echo esc_html($author_name); ?>
                                    </a>

                                    <div class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs font-medium text-[var(--color-text-muted)]">
                                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                            <?php echo esc_html(get_the_date()); ?>
                                        </time>

                                        <span aria-hidden="true">&bull;</span>

                                        <span>
                                            <?php echo esc_html(wordpress_demo_theme_reading_time($post_id)); ?>
                                            <?php esc_html_e('min read', 'wordpress-demo-theme'); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex w-full shrink-0 items-center justify-center gap-2 sm:w-auto" data-post-interactions data-post-id="<?php echo esc_attr($post_id); ?>" data-interaction-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-interaction-nonce="<?php echo esc_attr(wp_create_nonce('wordpress_demo_theme_post_interaction')); ?>" data-interaction-error="<?php esc_attr_e('Could not update this post. Please try again.', 'wordpress-demo-theme'); ?>">
                                <button type="button" class="inline-flex h-9 min-w-9 items-center justify-center gap-1.5 rounded-full bg-[var(--color-action-surface)] px-2.5 text-xs font-medium text-[var(--color-text-secondary)] transition-all duration-200 hover:bg-[var(--color-error-subtle)] hover:text-[var(--color-error)] active:scale-95 disabled:cursor-wait disabled:opacity-60 aria-pressed:bg-[var(--color-error-subtle)] aria-pressed:text-[var(--color-error)] sm:h-9 sm:min-w-9 sm:gap-2 sm:px-3 sm:text-sm" data-post-interaction="like" aria-pressed="<?php echo $post_interactions['liked'] ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr($post_interactions['liked'] ? __('Unlike this post', 'wordpress-demo-theme') : __('Like this post', 'wordpress-demo-theme')); ?>" data-active-label="<?php esc_attr_e('Unlike this post', 'wordpress-demo-theme'); ?>" data-inactive-label="<?php esc_attr_e('Like this post', 'wordpress-demo-theme'); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 sm:size-5" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="<?php echo $post_interactions['liked'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                        <path d="M10.4107 19.9677C7.58942 17.858 2 13.0348 2 8.69444C2 5.82563 4.10526 3.5 7 3.5C8.5 3.5 10 4 12 6C14 4 15.5 3.5 17 3.5C19.8947 3.5 22 5.82563 22 8.69444C22 13.0348 16.4106 17.858 13.5893 19.9677C12.6399 20.6776 11.3601 20.6776 10.4107 19.9677Z"></path>
                                    </svg>

                                    <span data-post-like-count data-count="<?php echo esc_attr($post_interactions['like_count']); ?>"><?php echo esc_html(wordpress_demo_theme_format_compact_number($post_interactions['like_count'])); ?></span>
                                </button>

                                <?php if(comments_open() || get_comments_number()) : ?>
                                    <a href="<?php echo esc_url(get_comments_link()); ?>" class="inline-flex h-9 min-w-9 items-center justify-center gap-1.5 rounded-full bg-[var(--color-action-surface)] px-2.5 text-xs font-medium text-[var(--color-text-secondary)] transition-all duration-200 hover:bg-[var(--color-primary-subtle)] hover:text-[var(--color-primary-hover)] active:scale-95 sm:h-9 sm:min-w-9 sm:gap-2 sm:px-3 sm:text-sm" title="<?php esc_attr_e('Comments', 'wordpress-demo-theme'); ?>" aria-label="<?php echo esc_attr(sprintf(__('View %s comments', 'wordpress-demo-theme'), number_format_i18n(get_comments_number()))); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 sm:size-5" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                            <path d="M8 13.5H16M8 8.5H12" stroke-linejoin="round"></path>
                                            <path d="M6.09881 19C4.7987 18.8721 3.82475 18.4816 3.17157 17.8284C2 16.6569 2 14.7712 2 11V10.5C2 6.72876 2 4.84315 3.17157 3.67157C4.34315 2.5 6.22876 2.5 10 2.5H14C17.7712 2.5 19.6569 2.5 20.8284 3.67157C22 4.84315 22 6.72876 22 10.5V11C22 14.7712 22 16.6569 20.8284 17.8284C19.6569 19 17.7712 19 14 19C13.4395 19.0125 12.9931 19.0551 12.5546 19.155C11.3562 19.4309 10.2465 20.0441 9.14987 20.5789C7.58729 21.3408 6.806 21.7218 6.31569 21.3651C5.37769 20.6665 6.29454 18.5019 6.5 17.5"></path>
                                        </svg>

                                        <span><?php echo esc_html(wordpress_demo_theme_format_compact_number(get_comments_number())); ?></span>
                                    </a>
                                <?php endif; ?>

                                <span class="mx-1 text-[var(--color-border-strong)]" aria-hidden="true">/</span>

                                <button type="button" class="inline-flex size-9 items-center justify-center rounded-full bg-[var(--color-action-surface)] text-[var(--color-text-secondary)] transition-all duration-200 hover:bg-[var(--color-primary-subtle)] hover:text-[var(--color-primary-hover)] active:scale-95 disabled:cursor-wait disabled:opacity-60 aria-pressed:bg-[var(--color-primary-subtle)] aria-pressed:text-[var(--color-primary)] sm:size-9" data-post-interaction="save" aria-pressed="<?php echo $post_interactions['saved'] ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr($post_interactions['saved'] ? __('Remove from saved posts', 'wordpress-demo-theme') : __('Save this post', 'wordpress-demo-theme')); ?>" data-active-label="<?php esc_attr_e('Remove from saved posts', 'wordpress-demo-theme'); ?>" data-inactive-label="<?php esc_attr_e('Save this post', 'wordpress-demo-theme'); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 sm:size-5" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="<?php echo $post_interactions['saved'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 17.9808V9.70753C4 6.07416 4 4.25748 5.17157 3.12874C6.34315 2 8.22876 2 12 2C15.7712 2 17.6569 2 18.8284 3.12874C20 4.25748 20 6.07416 20 9.70753V17.9808C20 20.2867 20 21.4396 19.2272 21.8523C17.7305 22.6514 14.9232 19.9852 13.59 19.1824C12.8168 18.7168 12.4302 18.484 12 18.484C11.5698 18.484 11.1832 18.7168 10.41 19.1824C9.0768 19.9852 6.26947 22.6514 4.77285 21.8523C4 21.4396 4 20.2867 4 17.9808Z"></path>
                                    </svg>
                                </button>
                                
                                <span class="sr-only" data-post-interaction-status role="status" aria-live="polite"></span>

                                <div class="relative">
                                    <button type="button" class="inline-flex size-9 items-center justify-center rounded-full bg-[var(--color-action-surface)] text-[var(--color-text-secondary)] transition-all duration-200 hover:bg-[var(--color-primary-subtle)] hover:text-[var(--color-primary-hover)] active:scale-95 sm:size-9" title="<?php esc_attr_e('Share', 'wordpress-demo-theme'); ?>" aria-label="<?php esc_attr_e('Share article', 'wordpress-demo-theme'); ?>" aria-expanded="false" aria-haspopup="true" data-post-share-button>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 sm:size-5" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M8 7C8 7 10.1958 4.28386 11.4044 3.23889C11.5987 3.0709 11.8169 2.99152 12.0337 3.00072C12.2282 3.00897 12.4215 3.08844 12.5958 3.23912C13.8041 4.28428 16 7 16 7M12.0337 4L12.0337 15"></path>
                                            <path d="M8 11C6.59987 11 5.8998 11 5.36502 11.2725C4.89462 11.5122 4.51217 11.8946 4.27248 12.365C4 12.8998 4 13.5999 4 15V16C4 18.357 4 19.5355 4.73223 20.2678C5.46447 21 6.64298 21 9 21H15C17.357 21 18.5355 21 19.2678 20.2678C20 19.5355 20 18.357 20 16V15C20 13.5999 20 12.8998 19.7275 12.365C19.4878 11.8946 19.1054 11.5122 18.635 11.2725C18.1002 11 17.4001 11 16 11"></path>
                                        </svg>
                                    </button>

                                    <div class="absolute right-0 top-full z-50 mt-2 hidden w-44 overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-card)] p-1.5 text-sm text-[var(--color-text-secondary)] shadow-[var(--shadow-md)]" data-post-share-menu>
                                        <a href="<?php echo esc_url('https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($post_url)); ?>" target="_blank" rel="noopener noreferrer" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 transition-colors hover:text-[var(--color-primary)]">
                                            <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.093 10.125 24v-8.437H7.078v-3.49h3.047V9.413c0-3.025 1.792-4.698 4.533-4.698 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.266h3.328l-.532 3.49h-2.796V24C19.612 23.093 24 18.1 24 12.073Z"></path>
                                            </svg>
                                            <span><?php esc_html_e('Facebook', 'wordpress-demo-theme'); ?></span>
                                        </a>

                                        <a href="<?php echo esc_url('https://twitter.com/intent/tweet?url=' . rawurlencode($post_url) . '&text=' . rawurlencode(get_the_title())); ?>" target="_blank" rel="noopener noreferrer" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 transition-colors hover:text-[var(--color-primary)]">
                                            <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                <path d="M18.9 2H22l-6.8 7.8L23.2 22h-6.3l-5-7.4L5.5 22H2.4l7.3-8.4L1.9 2h6.5l4.5 6.8L18.9 2zm-1.1 17.9h1.7L7.4 4H5.6l12.2 15.9z"></path>
                                            </svg>
                                            <span><?php esc_html_e('X / Twitter', 'wordpress-demo-theme'); ?></span>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="relative">
                                    <button type="button" class="inline-flex size-9 items-center justify-center rounded-full bg-[var(--color-action-surface)] text-[var(--color-text-secondary)] transition-all duration-200 hover:bg-[var(--color-primary-subtle)] hover:text-[var(--color-primary-hover)] active:scale-95 sm:size-9" title="<?php esc_attr_e('Share', 'wordpress-demo-theme'); ?>" aria-label="<?php esc_attr_e('Share article', 'wordpress-demo-theme'); ?>" aria-expanded="false" aria-haspopup="true" data-post-share-button>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 sm:size-5" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 12V11.5M12 5V4.5M12 19V18.5M13 12C13 11.4477 12.5523 11 12 11C11.4477 11 11 11.4477 11 12C11 12.5523 11.4477 13 12 13C12.5523 13 13 12.5523 13 12ZM13 5C13 4.44772 12.5523 4 12 4C11.4477 4 11 4.44772 11 5C11 5.55228 11.4477 6 12 6C12.5523 6 13 5.55228 13 5ZM13 19C13 18.4477 12.5523 18 12 18C11.4477 18 11 18.4477 11 19C11 19.5523 11.4477 20 12 20C12.5523 20 13 19.5523 13 19Z"></path>
                                        </svg>
                                    </button>

                                    <div class="absolute right-0 top-full z-50 mt-2 hidden w-44 overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-card)] p-1.5 text-sm text-[var(--color-text-secondary)] shadow-[var(--shadow-md)]" data-post-share-menu>
                                        <button type="button" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-left transition-colors hover:text-[var(--color-primary)]" data-copy-post-link data-post-url="<?php echo esc_url($post_url); ?>" data-copied-text="<?php esc_attr_e('Copied!', 'wordpress-demo-theme'); ?>" data-copy-error-text="<?php esc_attr_e('Copy failed. Try again.', 'wordpress-demo-theme'); ?>">
                                            <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                                <rect x="8" y="8" width="12" height="12" rx="2"></rect>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 8V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h3"></path>
                                            </svg>
                                            <span data-copy-post-link-text role="status" aria-live="polite"><?php esc_html_e('Copy link', 'wordpress-demo-theme'); ?></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>

                    <div class="grid min-w-0 grid-cols-1 gap-8 lg:grid-cols-[minmax(0,13fr)_minmax(0,7fr)] lg:items-start">
                        <div class="min-w-0">
                            <?php if(has_post_thumbnail() && (!function_exists('wordpress_demo_theme_post_header_uses_featured_image') || !wordpress_demo_theme_post_header_uses_featured_image())) : ?>
                                <figure class="group mb-8 overflow-hidden rounded-2xl bg-[var(--color-surface-soft)] md:mb-10">
                                    <?php
                                    the_post_thumbnail('full', array(
                                        'class' => 'block h-auto w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105 motion-reduce:transition-none motion-reduce:transform-none',
                                        'sizes' => '(max-width: 768px) 100vw, (max-width: 1024px) 974px, (max-width: 1280px) 1140px, (max-width: 1536px) 1320px, 1520px'
                                    ));
                                    ?>

                                    <?php if(get_the_post_thumbnail_caption()) : ?>
                                        <figcaption class="relative z-10 bg-[var(--color-surface-soft)] px-4 py-3 text-center text-xs leading-6 text-[var(--color-text-muted)] sm:text-sm">
                                            <?php echo esc_html(get_the_post_thumbnail_caption()); ?>
                                        </figcaption>
                                    <?php endif; ?>
                                </figure>
                            <?php endif; ?>

                            <div class="entry-content wordpress-demo-theme-content prose max-w-none min-w-0 break-words text-base leading-8 text-[var(--color-text-primary)] [&_a]:text-[var(--color-primary)] [&_a]:underline [&_a:hover]:opacity-80 [&_blockquote]:my-7 [&_blockquote]:border-l-4 [&_blockquote]:border-[var(--color-primary)] [&_blockquote]:pl-5 [&_blockquote]:italic [&_figcaption]:mt-2 [&_figcaption]:text-center [&_figcaption]:text-sm [&_figcaption]:text-[var(--color-text-muted)] [&_figure]:my-8 [&_h2]:mb-4 [&_h2]:mt-12 [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-[var(--color-text-heading)] [&_h3]:mb-3 [&_h3]:mt-9 [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:text-[var(--color-text-heading)] [&_h4]:mb-3 [&_h4]:mt-7 [&_h4]:text-lg [&_h4]:font-semibold [&_h4]:text-[var(--color-text-heading)] [&_hr]:my-10 [&_hr]:border-[var(--color-border)] [&_img]:h-auto [&_img]:max-w-full [&_li]:mb-2 [&_ol]:my-5 [&_ol]:list-decimal [&_ol]:pl-6 [&_p]:mb-6 [&_pre]:my-6 [&_pre]:overflow-x-auto [&_pre]:rounded-xl [&_pre]:bg-[var(--color-surface-soft)] [&_pre]:p-5 [&_ul]:my-5 [&_ul]:list-disc [&_ul]:pl-6">
                                <?php the_content(); ?>

                                <?php
                                wp_link_pages(array(
                                    'before' => '<div class="mt-8 flex flex-wrap items-center gap-2 text-sm font-medium text-[var(--color-text-secondary)]">' . esc_html__('Pages:', 'wordpress-demo-theme'),
                                    'after' => '</div>',
                                    'link_before' => '<span class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-lg border border-[var(--color-border)] px-3">',
                                    'link_after' => '</span>'
                                ));
                                ?>
                            </div>

                            <?php
                            $post_tags = get_the_tags();

                            if($post_tags && !is_wp_error($post_tags)) :
                                ?>
                                <nav class="mt-8 flex flex-wrap items-center gap-2" aria-label="<?php esc_attr_e('Post tags', 'wordpress-demo-theme'); ?>">
                                    <span class="mr-1 text-sm font-semibold text-[var(--color-text-primary)]">
                                        <?php esc_html_e('Tags:', 'wordpress-demo-theme'); ?>
                                    </span>

                                    <?php foreach($post_tags as $post_tag) : ?>
                                        <a href="<?php echo esc_url(get_tag_link($post_tag->term_id)); ?>" rel="tag" class="inline-flex min-h-9 max-w-full items-center break-words rounded-full border border-[var(--color-border)] bg-[var(--color-action-surface)] px-3 py-1.5 text-xs font-medium text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-primary-subtle)] hover:text-[var(--color-primary)]">
                                            #<?php echo esc_html($post_tag->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </nav>
                            <?php endif; ?>

                            <?php
                            $previous_post = get_previous_post();
                            $next_post = get_next_post();

                            if($previous_post || $next_post) :
                            ?>
                                <nav class="mt-8 flex items-start justify-between gap-4 border-t border-[var(--color-divider)] pt-5" aria-label="<?php esc_attr_e('Post navigation', 'wordpress-demo-theme'); ?>">
                                    <div class="min-w-0 flex-1">
                                        <?php if($previous_post) : ?>
                                            <a href="<?php echo esc_url(get_permalink($previous_post)); ?>" class="group inline-flex max-w-full items-center gap-2 rounded-lg py-2 text-left text-sm transition-colors hover:text-[var(--color-primary)]">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-[var(--color-text-muted)] transition-colors group-hover:text-[var(--color-primary)]" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M15 18l-6-6 6-6"></path>
                                                </svg>

                                                <span class="min-w-0">
                                                    <span class="block text-xs font-medium text-[var(--color-text-muted)]">
                                                        <?php esc_html_e('Previous', 'wordpress-demo-theme'); ?>
                                                    </span>

                                                    <span class="mt-1 block max-w-[180px] truncate font-medium text-[var(--color-text-primary)] transition-colors group-hover:text-[var(--color-primary)] sm:max-w-[240px]">
                                                        <?php echo esc_html(get_the_title($previous_post)); ?>
                                                    </span>
                                                </span>
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex min-w-0 flex-1 justify-end">
                                        <?php if($next_post) : ?>
                                            <a href="<?php echo esc_url(get_permalink($next_post)); ?>" class="group inline-flex max-w-full items-center justify-end gap-2 rounded-lg py-2 text-right text-sm transition-colors hover:text-[var(--color-primary)]">
                                                <span class="min-w-0">
                                                    <span class="block text-xs font-medium text-[var(--color-text-muted)]">
                                                        <?php esc_html_e('Next', 'wordpress-demo-theme'); ?>
                                                    </span>

                                                    <span class="mt-1 block max-w-[180px] truncate font-medium text-[var(--color-text-primary)] transition-colors group-hover:text-[var(--color-primary)] sm:max-w-[240px]">
                                                        <?php echo esc_html(get_the_title($next_post)); ?>
                                                    </span>
                                                </span>

                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-[var(--color-text-muted)] transition-colors group-hover:text-[var(--color-primary)]" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M9 18l6-6-6-6"></path>
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </nav>
                            <?php endif; ?>

                            <?php if(comments_open() || get_comments_number()) : ?>
                                <div id="post-comments" class="mt-12 border-t border-[var(--color-divider)] pt-8">
                                    <?php comments_template(); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <aside id="single-post-sidebar" class="hidden min-w-0 lg:sticky lg:top-24 lg:block lg:self-start">
                            <div class="space-y-6">
                                <div class="overflow-hidden rounded-xl border border-[var(--color-border)] bg-[var(--color-card)] shadow-[var(--shadow-sm)]" data-table-of-contents-card hidden>
                                    <div class="flex items-center justify-between border-b border-[var(--color-divider)] px-5 py-3">
                                        <h2 class="text-lg font-bold text-[var(--color-text-primary)]"><?php esc_html_e('Table of Contents', 'wordpress-demo-theme'); ?></h2>

                                        <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-[var(--color-primary-subtle)] text-[var(--color-primary)]">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                                <path d="M8 5.5L20 5.5"></path>
                                                <path d="M8 12.5L20 12.5"></path>
                                                <path d="M8 19.5L20 19.5"></path>
                                                <path d="M4.375 5.5H4.25M4.5 5.5C4.5 5.63807 4.38807 5.75 4.25 5.75C4.11193 5.75 4 5.63807 4 5.5C4 5.36193 4.11193 5.25 4.25 5.25C4.38807 5.25 4.5 5.36193 4.5 5.5Z" stroke-linejoin="round"></path>
                                                <path d="M4.375 12.5H4.25M4.5 12.5C4.5 12.6381 4.38807 12.75 4.25 12.75C4.11193 12.75 4 12.6381 4 12.5C4 12.3619 4.11193 12.25 4.25 12.25C4.38807 12.25 4.5 12.3619 4.5 12.5Z" stroke-linejoin="round"></path>
                                                <path d="M4.375 19.5H4.25M4.5 19.5C4.5 19.6381 4.38807 19.75 4.25 19.75C4.11193 19.75 4 19.6381 4 19.5C4 19.3619 4.11193 19.25 4.25 19.25C4.38807 19.25 4.5 19.3619 4.5 19.5Z" stroke-linejoin="round"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <nav class="relative px-4 py-2" aria-label="<?php esc_attr_e('Table of Contents', 'wordpress-demo-theme'); ?>">
                                        <div class="absolute bottom-[22px] left-[38px] top-[22px] w-px -translate-x-1/2 bg-[var(--color-divider)]"></div>
                                        <div data-table-of-contents></div>
                                    </nav>
                                </div>

                                <?php if(!empty($popular_categories)) : ?>
                                <div class="overflow-hidden rounded-xl border border-[var(--color-border)] bg-[var(--color-card)] shadow-[var(--shadow-sm)]">
                                    <div class="border-b border-[var(--color-divider)] px-5 py-4">
                                        <h2 class="mt-1 text-lg font-bold text-[var(--color-text-primary)]"><?php esc_html_e('Popular Categories', 'wordpress-demo-theme'); ?></h2>
                                    </div>

                                    <div class="flex flex-wrap gap-2 px-4 py-3">
                                        <?php foreach($popular_categories as $popular_category) : ?>
                                            <a href="<?php echo esc_url(get_category_link($popular_category->term_id)); ?>" class="inline-flex min-h-9 max-w-full items-center break-words rounded-full border border-[var(--color-border)] bg-[var(--color-action-surface)] px-3 py-1.5 text-xs font-medium text-[var(--color-text-secondary)] transition-colors hover:bg-[var(--color-primary-subtle)] hover:text-[var(--color-primary)]">
                                                <?php echo esc_html($popular_category->name); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </aside>
                    </div>
                    
                    <button type="button" id="mobile-toc-toggle" class="fixed right-0 top-[30%] z-40 hidden size-10 items-center justify-center rounded-full border border-[var(--color-primary-subtle-border)] bg-[var(--color-card)] text-[var(--color-primary)] shadow-[var(--shadow-md)] transition duration-200 hover:bg-[var(--color-primary-subtle)] lg:hidden" aria-label="<?php esc_attr_e('Open table of contents', 'wordpress-demo-theme'); ?>" aria-expanded="false" data-mobile-toc-toggle>
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                            <path d="M8 5.5L20 5.5"></path>
                            <path d="M8 12.5L20 12.5"></path>
                            <path d="M8 19.5L20 19.5"></path>
                            <path d="M4.375 5.5H4.25M4.5 5.5C4.5 5.63807 4.38807 5.75 4.25 5.75C4.11193 5.75 4 5.63807 4 5.5C4 5.36193 4.11193 5.25 4.25 5.25C4.38807 5.25 4.5 5.36193 4.5 5.5Z" stroke-linejoin="round"></path>
                            <path d="M4.375 12.5H4.25M4.5 12.5C4.5 12.6381 4.38807 12.75 4.25 12.75C4.11193 12.75 4 12.6381 4 12.5C4 12.3619 4.11193 12.25 4.25 12.25C4.38807 12.25 4.5 12.3619 4.5 12.5Z" stroke-linejoin="round"></path>
                            <path d="M4.375 19.5H4.25M4.5 19.5C4.5 19.6381 4.38807 19.75 4.25 19.75C4.11193 19.75 4 19.6381 4 19.5C4 19.3619 4.11193 19.25 4.25 19.25C4.38807 19.25 4.5 19.3619 4.5 19.5Z" stroke-linejoin="round"></path>
                        </svg>
                    </button>

                    <div id="mobile-toc-overlay" class="pointer-events-none invisible fixed inset-0 z-40 bg-[var(--color-overlay)] opacity-0 backdrop-blur-[1px] transition-opacity duration-200 lg:hidden" data-mobile-toc-overlay></div>

                    <aside id="mobile-toc-drawer" class="pointer-events-none invisible fixed right-4 top-[30%] z-50 w-[calc(100%-2rem)] max-w-sm translate-y-2 scale-95 overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] opacity-0 shadow-[var(--shadow-md)] transition-all duration-200 ease-out lg:hidden" data-mobile-toc-drawer>
                        <div class="flex items-center justify-between border-b border-[var(--color-divider)] px-4 py-3">
                            <h2 class="text-base font-bold text-[var(--color-text-primary)]"><?php esc_html_e('Table of Contents', 'wordpress-demo-theme'); ?></h2>

                            <button type="button" id="mobile-toc-close" class="flex size-8 items-center justify-center rounded-full bg-[var(--color-action-surface)] text-[var(--color-text-secondary)] transition hover:bg-[var(--color-primary-subtle)] hover:text-[var(--color-primary)]" aria-label="<?php esc_attr_e('Close table of contents', 'wordpress-demo-theme'); ?>" data-mobile-toc-close>
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 6L6.00081 17.9992M17.9992 18L6 6.00085"></path>
                                </svg>
                            </button>
                        </div>

                        <nav class="relative max-h-[50vh] overflow-y-auto px-3 py-2 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden" aria-label="<?php esc_attr_e('Table of Contents', 'wordpress-demo-theme'); ?>">
                            <div class="absolute bottom-[20px] left-[34px] top-[20px] w-px -translate-x-1/2 bg-[var(--color-divider)]"></div>
                            <div data-mobile-table-of-contents></div>
                        </nav>
                    </aside>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
