<?php
/**
 * Page: Index
 *
 * Fallback template for WordPress content listings.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="main-content" class="flex-1 py-6 sm:py-8 lg:py-10">
    <div class="container">
        <div class="mx-auto max-w-7xl">
            <?php
            wordpress_demo_theme_breadcrumb(array(
                'items' => array(
                    array('title' => __('Home', 'wordpress-demo-theme'), 'url' => home_url('/')),
                    array('title' => __('Latest Posts', 'wordpress-demo-theme'), 'url' => '')
                )
            ));
            ?>

            <header class="mb-6 md:mb-8">
                <h1 class="text-2xl font-bold tracking-tight text-[var(--color-text-primary)] sm:text-3xl">
                    <?php esc_html_e('Latest Posts', 'wordpress-demo-theme'); ?>
                </h1>
            </header>

            <?php if(have_posts()) : ?>
                <div class="grid min-w-0 grid-cols-1 gap-5 lg:grid-cols-2">
                    <?php while(have_posts()) : the_post(); ?>
                        <?php wordpress_demo_theme_blog_card(get_the_ID()); ?>
                    <?php endwhile; ?>
                </div>

                <?php if($GLOBALS['wp_query']->max_num_pages > 1) : ?>
                    <nav class="mt-8 text-sm text-[var(--color-text-secondary)] [&_.nav-links]:flex [&_.nav-links]:flex-wrap [&_.nav-links]:items-center [&_.nav-links]:justify-center [&_.nav-links]:gap-2 [&_.page-numbers]:inline-flex [&_.page-numbers]:min-h-10 [&_.page-numbers]:min-w-10 [&_.page-numbers]:items-center [&_.page-numbers]:justify-center [&_.page-numbers]:rounded-lg [&_.page-numbers]:border [&_.page-numbers]:border-[var(--color-border)] [&_.page-numbers]:px-3 [&_.page-numbers]:transition-colors [&_.page-numbers:hover]:bg-[var(--color-primary-subtle)] [&_.current]:bg-[var(--color-primary-subtle)] [&_.current]:font-semibold [&_.current]:text-[var(--color-primary)]" aria-label="<?php esc_attr_e('Posts navigation', 'wordpress-demo-theme'); ?>">
                        <?php the_posts_pagination(array(
                            'mid_size' => 2,
                            'prev_text' => __('Previous', 'wordpress-demo-theme'),
                            'next_text' => __('Next', 'wordpress-demo-theme'),
                            'screen_reader_text' => __('Posts navigation', 'wordpress-demo-theme')
                        )); ?>
                    </nav>
                <?php endif; ?>
            <?php else : ?>
                <div class="rounded-xl border border-dashed border-[var(--color-border-strong)] bg-[var(--color-surface-soft)] px-4 py-8 text-center text-sm text-[var(--color-text-muted)]">
                    <?php esc_html_e('No posts found.', 'wordpress-demo-theme'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
