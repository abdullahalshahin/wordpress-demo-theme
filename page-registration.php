<?php
/**
 * Page: Registration
 *
 * Displays the centered frontend user registration form.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

$registration_error = isset($_GET['registration_error']) && is_string($_GET['registration_error']) ? sanitize_key(wp_unslash($_GET['registration_error'])) : '';
$site_name = get_bloginfo('name');
$privacy_policy_url = get_privacy_policy_url() ?: home_url('/privacy-policy/');
$terms_url = home_url('/terms/');
$error_messages = array(
    'security' => __('Your session expired. Please try again.', 'wordpress-demo-theme'),
    'invalid_details' => __('Enter your first name, last name, and a valid email address.', 'wordpress-demo-theme'),
    'email_exists' => __('An account already exists for this email address.', 'wordpress-demo-theme'),
    'weak_password' => __('Use at least 6 characters.', 'wordpress-demo-theme'),
    'password_mismatch' => __('The passwords do not match.', 'wordpress-demo-theme'),
    'terms_required' => __('You must agree to the Terms and Privacy Policy.', 'wordpress-demo-theme'),
    'create_failed' => __('Your account could not be created. Please try again.', 'wordpress-demo-theme')
);
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
            <div class="w-full max-w-2xl rounded-2xl border border-[var(--color-border)] bg-[var(--color-card)] p-6 shadow-[var(--shadow-md)] sm:p-8 lg:p-10">
                <div class="flex items-center justify-between gap-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[var(--color-primary)]"><?php esc_html_e('Create account', 'wordpress-demo-theme'); ?></p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--color-text-secondary)] transition hover:text-[var(--color-primary)]">
                        <span aria-hidden="true">&larr;</span>
                        <?php esc_html_e('Back to home', 'wordpress-demo-theme'); ?>
                    </a>
                </div>

                <header class="mt-3">
                    <h1 class="text-2xl font-bold tracking-tight text-[var(--color-text-heading)] sm:text-3xl"><?php echo esc_html(sprintf(__('Join %s', 'wordpress-demo-theme'), $site_name)); ?></h1>
                    <p class="mt-2 text-sm leading-6 text-[var(--color-text-secondary)] sm:text-base"><?php esc_html_e('Create an account to save offers and manage your activity.', 'wordpress-demo-theme'); ?></p>
                </header>

                <?php if($registration_error) : ?>
                    <div class="mt-6 rounded-lg border border-[var(--color-error-border)] bg-[var(--color-error-subtle)] px-4 py-3 text-sm text-[var(--color-error)]" role="alert">
                        <?php echo esc_html($error_messages[$registration_error] ?? $error_messages['create_failed']); ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?php echo esc_url(home_url('/registration/')); ?>" class="mt-7 space-y-5">
                        <?php wp_nonce_field('wordpress_demo_theme_registration', 'wordpress_demo_theme_registration_nonce'); ?>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="registration_first_name" class="mb-2 block text-sm font-medium text-[var(--color-text-primary)]"><?php esc_html_e('First name', 'wordpress-demo-theme'); ?></label>
                                <input id="registration_first_name" name="first_name" type="text" required autocomplete="given-name" placeholder="<?php esc_attr_e('First name', 'wordpress-demo-theme'); ?>" class="h-12 w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-input)] px-4 text-sm text-[var(--color-text-primary)] outline-none transition focus:border-[var(--color-primary)] focus:ring-4 focus:ring-[var(--color-focus-ring)]">
                            </div>

                            <div>
                                <label for="registration_last_name" class="mb-2 block text-sm font-medium text-[var(--color-text-primary)]"><?php esc_html_e('Last name', 'wordpress-demo-theme'); ?></label>
                                <input id="registration_last_name" name="last_name" type="text" required autocomplete="family-name" placeholder="<?php esc_attr_e('Last name', 'wordpress-demo-theme'); ?>" class="h-12 w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-input)] px-4 text-sm text-[var(--color-text-primary)] outline-none transition focus:border-[var(--color-primary)] focus:ring-4 focus:ring-[var(--color-focus-ring)]">
                            </div>
                        </div>

                        <div>
                            <label for="registration_email" class="mb-2 block text-sm font-medium text-[var(--color-text-primary)]"><?php esc_html_e('Your email', 'wordpress-demo-theme'); ?></label>
                            <input id="registration_email" name="email" type="email" required autocomplete="email" placeholder="<?php esc_attr_e('name@example.com', 'wordpress-demo-theme'); ?>" class="h-12 w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-input)] px-4 text-sm text-[var(--color-text-primary)] outline-none transition focus:border-[var(--color-primary)] focus:ring-4 focus:ring-[var(--color-focus-ring)]">
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label for="registration_password" class="mb-2 block text-sm font-medium text-[var(--color-text-primary)]"><?php esc_html_e('Password', 'wordpress-demo-theme'); ?></label>
                                <div class="relative">
                                    <input id="registration_password" name="password" type="password" required minlength="6" autocomplete="new-password" placeholder="<?php esc_attr_e('Password', 'wordpress-demo-theme'); ?>" class="h-12 w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-input)] px-4 pr-12 text-sm text-[var(--color-text-primary)] outline-none transition focus:border-[var(--color-primary)] focus:ring-4 focus:ring-[var(--color-focus-ring)]" data-password-input>
                                    <button type="button" class="absolute right-3 top-1/2 inline-flex size-8 -translate-y-1/2 items-center justify-center rounded-full text-[var(--color-text-muted)] transition hover:bg-[var(--color-action-surface)] hover:text-[var(--color-text-primary)]" aria-label="<?php esc_attr_e('Show password', 'wordpress-demo-theme'); ?>" data-password-toggle data-show-label="<?php esc_attr_e('Show password', 'wordpress-demo-theme'); ?>" data-hide-label="<?php esc_attr_e('Hide password', 'wordpress-demo-theme'); ?>">
                                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6S2.25 12 2.25 12Z"></path><circle cx="12" cy="12" r="2.5"></circle></svg>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label for="confirm_password" class="mb-2 block text-sm font-medium text-[var(--color-text-primary)]"><?php esc_html_e('Confirm password', 'wordpress-demo-theme'); ?></label>
                                <div class="relative">
                                    <input id="confirm_password" name="confirm_password" type="password" required minlength="6" autocomplete="new-password" placeholder="<?php esc_attr_e('Repeat password', 'wordpress-demo-theme'); ?>" class="h-12 w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-input)] px-4 pr-12 text-sm text-[var(--color-text-primary)] outline-none transition focus:border-[var(--color-primary)] focus:ring-4 focus:ring-[var(--color-focus-ring)]" data-password-input>
                                    <button type="button" class="absolute right-3 top-1/2 inline-flex size-8 -translate-y-1/2 items-center justify-center rounded-full text-[var(--color-text-muted)] transition hover:bg-[var(--color-action-surface)] hover:text-[var(--color-text-primary)]" aria-label="<?php esc_attr_e('Show password', 'wordpress-demo-theme'); ?>" data-password-toggle data-show-label="<?php esc_attr_e('Show password', 'wordpress-demo-theme'); ?>" data-hide-label="<?php esc_attr_e('Hide password', 'wordpress-demo-theme'); ?>">
                                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6S2.25 12 2.25 12Z"></path><circle cx="12" cy="12" r="2.5"></circle></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-[var(--color-border)] bg-[var(--color-surface-soft)] px-4 py-2 text-sm leading-6 text-[var(--color-text-secondary)]">
                            <input type="checkbox" name="accept_terms" value="1" required class="mt-1 size-4 shrink-0 rounded border-[var(--color-border-strong)] text-[var(--color-primary)] focus:ring-[var(--color-focus-ring)]">
                            <span>
                                <?php echo esc_html(sprintf(__('I agree to the %s', 'wordpress-demo-theme'), $site_name)); ?>
                                <a href="<?php echo esc_url($terms_url); ?>" class="font-medium text-[var(--color-primary)] underline hover:text-[var(--color-primary-hover)]"><?php esc_html_e('Terms of Service', 'wordpress-demo-theme'); ?></a>
                                <?php esc_html_e('and', 'wordpress-demo-theme'); ?>
                                <a href="<?php echo esc_url($privacy_policy_url); ?>" class="font-medium text-[var(--color-primary)] underline hover:text-[var(--color-primary-hover)]"><?php esc_html_e('Privacy Policy', 'wordpress-demo-theme'); ?></a>.
                            </span>
                        </label>

                        <button type="submit" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-[var(--color-primary)] px-5 text-sm font-semibold text-[var(--color-primary-text)] transition hover:bg-[var(--color-primary-hover)] focus:outline-none focus:ring-4 focus:ring-[var(--color-focus-ring)]">
                            <?php esc_html_e('Join the community', 'wordpress-demo-theme'); ?>
                            <span aria-hidden="true">→</span>
                        </button>
                </form>

                <div class="mt-7 border-t border-[var(--color-border)] pt-6 text-center">
                    <p class="text-sm text-[var(--color-text-secondary)]"><?php echo esc_html(sprintf(__('Already a %s member?', 'wordpress-demo-theme'), $site_name)); ?></p>
                    <a href="<?php echo esc_url(home_url('/login/')); ?>" class="mt-1 inline-flex font-semibold text-[var(--color-primary)] hover:text-[var(--color-primary-hover)]"><?php esc_html_e('Sign in to your account', 'wordpress-demo-theme'); ?></a>
                </div>
            </div>
        </main>

        <?php wp_footer(); ?>
    </body>
</html>
