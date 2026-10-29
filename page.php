<?php
/**
 * Page: Standard Page
 *
 * Displays page content, featured images,
 * navigation, and comments.
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
            <?php while(have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('min-w-0'); ?>>
                    <?php
                    if(!is_front_page()) {
                        $breadcrumb_title = get_the_title();

                        wordpress_demo_theme_breadcrumb(array(
                            'items' => array(
                                array(
                                    'title' => __('Home', 'wordpress-demo-theme'),
                                    'url' => home_url('/')
                                ),
                                array(
                                    'title' => $breadcrumb_title !== '' ? $breadcrumb_title : __('Untitled', 'wordpress-demo-theme'),
                                    'url' => ''
                                )
                            )
                        ));
                    }
                    ?>

                    <header class="mb-6 md:mb-8">
                        <h1 class="text-2xl font-bold tracking-tight text-[var(--color-text-primary)] sm:text-3xl">
                            <?php echo esc_html(get_the_title() ?: __('Untitled', 'wordpress-demo-theme')); ?>
                        </h1>

                        <?php if(has_excerpt()) : ?>
                            <p class="mt-4 max-w-4xl text-base leading-7 text-[var(--color-text-secondary)] sm:text-lg sm:leading-8">
                                <?php echo esc_html(get_the_excerpt()); ?>
                            </p>
                        <?php endif; ?>
                    </header>

                    <?php if(has_post_thumbnail()) : ?>
                        <figure class="mb-8 overflow-hidden rounded-2xl bg-[var(--color-surface-soft)] md:mb-10">
                            <?php
                            the_post_thumbnail('full', array(
                                'class' => 'block h-auto w-full object-cover',
                                'sizes' => '(max-width: 768px) 100vw, (max-width: 1024px) 974px, (max-width: 1280px) 1140px, (max-width: 1536px) 1320px, 1520px'
                            ));
                            ?>

                            <?php if(get_the_post_thumbnail_caption()) : ?>
                                <figcaption class="px-4 py-3 text-center text-xs leading-6 text-[var(--color-text-muted)] sm:text-sm">
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

                    <?php if(comments_open() || get_comments_number()) : ?>
                        <div id="post-comments" class="mt-12 border-t border-[var(--color-divider)] pt-8">
                            <?php comments_template(); ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endwhile; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
