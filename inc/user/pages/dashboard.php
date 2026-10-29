<?php
/**
 * User Page: Dashboard
 *
 * Displays the main frontend user dashboard layout.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
$dashboard_section = wordpress_demo_theme_get_dashboard_section();
$display_name = $current_user->display_name ?: $current_user->user_login;

get_header();
?>

<main id="main-content" class="flex-1 py-8 sm:py-10 lg:py-12">
    <div class="container">
        <div class="grid min-w-0 gap-6 lg:grid-cols-[240px_minmax(0,1fr)] lg:gap-8">
            <aside class="h-fit overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-card)] shadow-[var(--shadow-sm)]">
                <div class="flex items-center gap-3 border-b border-[var(--color-border)] p-4">
                    <?php echo get_avatar($current_user->ID, 48, '', $display_name, array('class' => 'size-12 rounded-full object-cover')); ?>
                    <div class="min-w-0">
                        <strong class="block truncate text-sm text-[var(--color-text-primary)]"><?php echo esc_html($display_name); ?></strong>
                        <span class="block truncate text-xs text-[var(--color-text-muted)]"><?php echo esc_html($current_user->user_email); ?></span>
                    </div>
                </div>

                <nav class="p-2" aria-label="<?php esc_attr_e('Account navigation', 'wordpress-demo-theme'); ?>">
                    <a href="<?php echo esc_url(home_url('/dashboard/')); ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition <?php echo $dashboard_section === 'dashboard' ? 'bg-[var(--color-primary-subtle)] text-[var(--color-primary)]' : 'text-[var(--color-text-secondary)] hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-text-primary)]'; ?>">
                        <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"></path></svg>
                        <?php esc_html_e('Dashboard', 'wordpress-demo-theme'); ?>
                    </a>
                    <a href="<?php echo esc_url(home_url('/dashboard/profile/')); ?>" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition <?php echo $dashboard_section === 'profile' ? 'bg-[var(--color-primary-subtle)] text-[var(--color-primary)]' : 'text-[var(--color-text-secondary)] hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-text-primary)]'; ?>">
                        <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="3.5"></circle><path stroke-linecap="round" d="M5 20a7 7 0 0 1 14 0"></path></svg>
                        <?php esc_html_e('Profile', 'wordpress-demo-theme'); ?>
                    </a>
                    <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-[var(--color-error)] transition hover:bg-[var(--color-error-subtle)]">
                        <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 5H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h4m5-4 3-3-3-3m3 3H9"></path></svg>
                        <?php esc_html_e('Sign out', 'wordpress-demo-theme'); ?>
                    </a>
                </nav>
            </aside>

            <div class="min-w-0">
                <?php if($dashboard_section === 'profile') : ?>
                    <?php require WORDPRESS_DEMO_THEME_PATH . 'inc/user/pages/profile.php'; ?>
                <?php else : ?>
                    <header>
                        <p class="text-sm font-semibold text-[var(--color-primary)]"><?php esc_html_e('Account overview', 'wordpress-demo-theme'); ?></p>
                        <h1 class="mt-1 text-2xl font-bold tracking-tight text-[var(--color-text-heading)] sm:text-3xl"><?php echo esc_html(sprintf(__('Welcome, %s', 'wordpress-demo-theme'), $display_name)); ?></h1>
                        <p class="mt-2 text-sm leading-6 text-[var(--color-text-secondary)] sm:text-base"><?php esc_html_e('Manage your account information and activity from this dashboard.', 'wordpress-demo-theme'); ?></p>
                    </header>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <section class="rounded-2xl border border-[var(--color-border)] bg-[var(--color-card)] p-5 shadow-[var(--shadow-sm)]">
                            <h2 class="text-base font-semibold text-[var(--color-text-heading)]"><?php esc_html_e('Account details', 'wordpress-demo-theme'); ?></h2>
                            <dl class="mt-4 space-y-3 text-sm">
                                <div><dt class="text-[var(--color-text-muted)]"><?php esc_html_e('Display name', 'wordpress-demo-theme'); ?></dt><dd class="mt-1 break-words font-medium text-[var(--color-text-primary)]"><?php echo esc_html($display_name); ?></dd></div>
                                <div><dt class="text-[var(--color-text-muted)]"><?php esc_html_e('Email address', 'wordpress-demo-theme'); ?></dt><dd class="mt-1 break-words font-medium text-[var(--color-text-primary)]"><?php echo esc_html($current_user->user_email); ?></dd></div>
                                <div><dt class="text-[var(--color-text-muted)]"><?php esc_html_e('Member since', 'wordpress-demo-theme'); ?></dt><dd class="mt-1 font-medium text-[var(--color-text-primary)]"><?php echo esc_html(wp_date(get_option('date_format'), strtotime($current_user->user_registered))); ?></dd></div>
                            </dl>
                        </section>

                        <section class="rounded-2xl border border-[var(--color-border)] bg-[var(--color-card)] p-5 shadow-[var(--shadow-sm)]">
                            <h2 class="text-base font-semibold text-[var(--color-text-heading)]"><?php esc_html_e('Profile settings', 'wordpress-demo-theme'); ?></h2>
                            <p class="mt-2 text-sm leading-6 text-[var(--color-text-secondary)]"><?php esc_html_e('Keep your name, email address, website, and biography up to date.', 'wordpress-demo-theme'); ?></p>
                            <a href="<?php echo esc_url(home_url('/dashboard/profile/')); ?>" class="mt-5 inline-flex h-10 items-center justify-center rounded-full bg-[var(--color-primary)] px-5 text-sm font-semibold text-[var(--color-primary-text)] transition hover:bg-[var(--color-primary-hover)]"><?php esc_html_e('Edit profile', 'wordpress-demo-theme'); ?></a>
                        </section>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>
