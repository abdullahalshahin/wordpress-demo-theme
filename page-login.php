<?php
/**
 * Page: Login
 *
 * Displays the centered frontend user login form.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

$login_error = isset($_GET['login_error']) && is_string($_GET['login_error']) ? sanitize_key(wp_unslash($_GET['login_error'])) : '';
$redirect_to = isset($_GET['redirect_to']) && is_string($_GET['redirect_to']) ? wp_validate_redirect(wp_unslash($_GET['redirect_to']), home_url('/dashboard/')) : home_url('/dashboard/');
$privacy_policy_url = get_privacy_policy_url() ?: home_url('/privacy-policy/');
$terms_url = home_url('/terms/');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class('min-h-screen bg-[var(--color-background)] text-[var(--color-text-primary)] antialiased'); ?>>
        <?php wp_body_open(); ?>

        <main id="main-content" class="flex min-h-screen min-h-dvh items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
            <div class="w-full max-w-xl rounded-2xl border border-[var(--color-border)] bg-[var(--color-card)] p-6 shadow-[var(--shadow-md)] sm:p-8 lg:p-10">
                <div class="flex items-center justify-between gap-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[var(--color-primary)]"><?php esc_html_e('Welcome back', 'wordpress-demo-theme'); ?></p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--color-text-secondary)] transition hover:text-[var(--color-primary)]">
                        <span aria-hidden="true">&larr;</span>
                        <?php esc_html_e('Back to home', 'wordpress-demo-theme'); ?>
                    </a>
                </div>

                <header class="mt-3">
                    <h1 class="text-2xl font-bold tracking-tight text-[var(--color-text-heading)] sm:text-3xl"><?php esc_html_e('Sign in to your account', 'wordpress-demo-theme'); ?></h1>
                    <p class="mt-2 text-sm leading-6 text-[var(--color-text-secondary)] sm:text-base"><?php esc_html_e('Enter your email and password to continue.', 'wordpress-demo-theme'); ?></p>
                </header>

                <?php if($login_error) : ?>
                    <div class="mt-6 rounded-lg border border-[var(--color-error-border)] bg-[var(--color-error-subtle)] px-4 py-3 text-sm text-[var(--color-error)]" role="alert">
                        <?php echo $login_error === 'security' ? esc_html__('Your session expired. Please try again.', 'wordpress-demo-theme') : esc_html__('The email address, username, or password is incorrect.', 'wordpress-demo-theme'); ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?php echo esc_url(home_url('/login/')); ?>" class="mt-7 space-y-5">
                    <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>">
                    <?php wp_nonce_field('wordpress_demo_theme_login', 'wordpress_demo_theme_login_nonce'); ?>

                    <div>
                        <label for="user_login" class="mb-2 block text-sm font-medium text-[var(--color-text-primary)]"><?php esc_html_e('Email or username', 'wordpress-demo-theme'); ?></label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-[var(--color-text-muted)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75A2.25 2.25 0 0 1 6 4.5h12a2.25 2.25 0 0 1 2.25 2.25v10.5A2.25 2.25 0 0 1 18 19.5H6a2.25 2.25 0 0 1-2.25-2.25V6.75Z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 7.5 6.1 4.35a2.4 2.4 0 0 0 2.8 0L19.5 7.5"></path>
                            </svg>
                            <input id="user_login" name="user_login" type="text" required autocomplete="username" placeholder="<?php esc_attr_e('Email or username', 'wordpress-demo-theme'); ?>" class="h-12 w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-input)] pl-12 pr-4 text-sm text-[var(--color-text-primary)] outline-none transition focus:border-[var(--color-primary)] focus:ring-4 focus:ring-[var(--color-focus-ring)]">
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between gap-4">
                            <label for="user_password" class="text-sm font-medium text-[var(--color-text-primary)]"><?php esc_html_e('Password', 'wordpress-demo-theme'); ?></label>
                            <a href="<?php echo esc_url(wp_lostpassword_url(home_url('/login/'))); ?>" class="text-sm font-medium text-[var(--color-primary)] hover:text-[var(--color-primary-hover)]"><?php esc_html_e('Forgot password?', 'wordpress-demo-theme'); ?></a>
                        </div>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-4 top-1/2 size-5 -translate-y-1/2 text-[var(--color-text-muted)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <rect x="4" y="10" width="16" height="10" rx="2"></rect>
                                <path stroke-linecap="round" d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                            </svg>
                            <input id="user_password" name="user_password" type="password" required autocomplete="current-password" placeholder="<?php esc_attr_e('Enter your password', 'wordpress-demo-theme'); ?>" class="h-12 w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-input)] pl-12 pr-12 text-sm text-[var(--color-text-primary)] outline-none transition focus:border-[var(--color-primary)] focus:ring-4 focus:ring-[var(--color-focus-ring)]" data-password-input>
                            <button type="button" class="absolute right-3 top-1/2 inline-flex size-8 -translate-y-1/2 items-center justify-center rounded-full text-[var(--color-text-muted)] transition hover:bg-[var(--color-action-surface)] hover:text-[var(--color-text-primary)]" aria-label="<?php esc_attr_e('Show password', 'wordpress-demo-theme'); ?>" data-password-toggle data-show-label="<?php esc_attr_e('Show password', 'wordpress-demo-theme'); ?>" data-hide-label="<?php esc_attr_e('Hide password', 'wordpress-demo-theme'); ?>">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6S2.25 12 2.25 12Z"></path>
                                    <circle cx="12" cy="12" r="2.5"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <label class="flex w-fit cursor-pointer items-center gap-2.5 text-sm text-[var(--color-text-secondary)]">
                        <input type="checkbox" name="remember" value="1" class="size-4 rounded border-[var(--color-border-strong)] text-[var(--color-primary)] focus:ring-[var(--color-focus-ring)]">
                        <span><?php esc_html_e('Remember me', 'wordpress-demo-theme'); ?></span>
                    </label>

                    <button type="submit" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-[var(--color-primary)] px-5 text-sm font-semibold text-[var(--color-primary-text)] transition hover:bg-[var(--color-primary-hover)] focus:outline-none focus:ring-4 focus:ring-[var(--color-focus-ring)]">
                        <?php esc_html_e('Sign in', 'wordpress-demo-theme'); ?>
                        <span aria-hidden="true">→</span>
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-[var(--color-text-secondary)]">
                    <?php esc_html_e('New to the site?', 'wordpress-demo-theme'); ?>
                    <a href="<?php echo esc_url(home_url('/registration/')); ?>" class="font-semibold text-[var(--color-primary)] hover:text-[var(--color-primary-hover)]"><?php esc_html_e('Create an account', 'wordpress-demo-theme'); ?></a>
                </p>

                <p class="mx-auto mt-6 max-w-sm text-center text-xs leading-5 text-[var(--color-text-muted)]">
                    <?php esc_html_e('By signing in, you agree to our', 'wordpress-demo-theme'); ?>
                    <a href="<?php echo esc_url($terms_url); ?>" class="underline hover:text-[var(--color-primary)]"><?php esc_html_e('Terms', 'wordpress-demo-theme'); ?></a>
                    <?php esc_html_e('and', 'wordpress-demo-theme'); ?>
                    <a href="<?php echo esc_url($privacy_policy_url); ?>" class="underline hover:text-[var(--color-primary)]"><?php esc_html_e('Privacy Policy', 'wordpress-demo-theme'); ?></a>.
                </p>
            </div>
        </main>

        <?php wp_footer(); ?>
    </body>
</html>
