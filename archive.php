<?php
/**
 * Page: Archive
 *
 * Displays WordPress taxonomy, author, date, and post archives.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

get_header();

$archive_title = get_the_archive_title();
$archive_description = get_the_archive_description();
$posts_page_id = (int) get_option('page_for_posts');
$blog_title = $posts_page_id ? get_the_title($posts_page_id) : __('Blog', 'wordpress-demo-theme');
$blog_url = $posts_page_id ? get_permalink($posts_page_id) : home_url('/');
?>

<main id="main-content" class="flex-1 py-6 sm:py-8 lg:py-10">
    <div class="container">
        <div class="mx-auto max-w-7xl">
            <?php wordpress_demo_theme_breadcrumb(array(
                'items' => array(
                    array('title' => __('Home', 'wordpress-demo-theme'), 'url' => home_url('/')),
                    array('title' => $blog_title ?: __('Blog', 'wordpress-demo-theme'), 'url' => $blog_url),
                    array('title' => wp_strip_all_tags($archive_title) ?: __('Archive', 'wordpress-demo-theme'), 'url' => '')
                )
            )); ?>

            <header class="mb-6 md:mb-8">
                <h1 class="text-2xl font-bold tracking-tight text-[var(--color-text-primary)] sm:text-3xl">
                    <?php echo wp_kses_post($archive_title); ?>
                </h1>

                <?php if($archive_description) : ?>
                    <div class="mt-4 max-w-4xl text-base leading-7 text-[var(--color-text-secondary)] sm:text-lg sm:leading-8 [&_p]:mb-3 [&_a]:text-[var(--color-primary)] [&_a]:underline">
                        <?php echo wp_kses_post($archive_description); ?>
                    </div>
                <?php endif; ?>
            </header>

            <?php if(have_posts()) : ?>
                <div class="grid min-w-0 grid-cols-1 gap-5 lg:grid-cols-2">
                    <?php while(have_posts()) : the_post(); ?>
                        <?php if(get_post_type() === 'post') : ?>
                            <?php wordpress_demo_theme_blog_card(get_the_ID()); ?>
                        <?php else : ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('min-w-0 rounded-2xl border border-[var(--color-border)] bg-[var(--color-card)] p-5 shadow-[var(--shadow-sm)]'); ?>>
                                <h2 class="text-lg font-semibold text-[var(--color-text-primary)]">
                                    <a href="<?php the_permalink(); ?>" class="hover:text-[var(--color-primary)]"><?php echo esc_html(get_the_title() ?: __('Untitled', 'wordpress-demo-theme')); ?></a>
                                </h2>
                                <p class="mt-2 line-clamp-3 text-sm leading-6 text-[var(--color-text-secondary)]"><?php echo esc_html(get_the_excerpt()); ?></p>
                            </article>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </div>

                <?php if($GLOBALS['wp_query']->max_num_pages > 1) : ?>
                    <nav class="mt-8 text-sm text-[var(--color-text-secondary)] [&_.nav-links]:flex [&_.nav-links]:flex-wrap [&_.nav-links]:items-center [&_.nav-links]:justify-center [&_.nav-links]:gap-2 [&_.page-numbers]:inline-flex [&_.page-numbers]:min-h-10 [&_.page-numbers]:min-w-10 [&_.page-numbers]:items-center [&_.page-numbers]:justify-center [&_.page-numbers]:rounded-lg [&_.page-numbers]:border [&_.page-numbers]:border-[var(--color-border)] [&_.page-numbers]:px-3 [&_.page-numbers]:transition-colors [&_.page-numbers:hover]:bg-[var(--color-primary-subtle)] [&_.current]:bg-[var(--color-primary-subtle)] [&_.current]:font-semibold [&_.current]:text-[var(--color-primary)]" aria-label="<?php esc_attr_e('Archive navigation', 'wordpress-demo-theme'); ?>">
                        <?php the_posts_pagination(array(
                            'mid_size' => 2,
                            'prev_text' => __('Previous', 'wordpress-demo-theme'),
                            'next_text' => __('Next', 'wordpress-demo-theme'),
                            'screen_reader_text' => __('Archive navigation', 'wordpress-demo-theme')
                        )); ?>
                    </nav>
                <?php endif; ?>
            <?php else : ?>
                <div class="rounded-xl border border-dashed border-[var(--color-border-strong)] bg-[var(--color-surface-soft)] px-4 py-8 text-center text-sm text-[var(--color-text-muted)]">
                    <?php esc_html_e('No posts found in this archive.', 'wordpress-demo-theme'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
