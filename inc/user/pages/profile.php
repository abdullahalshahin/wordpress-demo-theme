<?php
/**
 * User Page: Profile
 *
 * Displays profile details and editing controls
 * in the frontend user dashboard.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

$current_user = isset($current_user) && $current_user instanceof WP_User ? $current_user : wp_get_current_user();
$profile_error = isset($_GET['profile_error']) && is_string($_GET['profile_error']) ? sanitize_key(wp_unslash($_GET['profile_error'])) : '';
$profile_errors = array(
    'security' => __('Your session expired. Please try again.', 'wordpress-demo-theme'),
    'invalid_details' => __('Enter a valid display name and email address.', 'wordpress-demo-theme'),
    'email_exists' => __('This email address is already used by another account.', 'wordpress-demo-theme'),
    'update_failed' => __('Your profile could not be updated. Please try again.', 'wordpress-demo-theme')
);
?>

<header>
    <p class="text-sm font-semibold text-[var(--color-primary)]"><?php esc_html_e('Account settings', 'wordpress-demo-theme'); ?></p>
    <h1 class="mt-1 text-2xl font-bold tracking-tight text-[var(--color-text-heading)] sm:text-3xl"><?php esc_html_e('Your profile', 'wordpress-demo-theme'); ?></h1>
    <p class="mt-2 text-sm leading-6 text-[var(--color-text-secondary)] sm:text-base"><?php esc_html_e('Update the personal information associated with your account.', 'wordpress-demo-theme'); ?></p>
</header>

<?php if(isset($_GET['profile_updated']) && sanitize_key(wp_unslash($_GET['profile_updated'])) === '1') : ?>
    <div class="mt-6 rounded-xl border border-[var(--color-success-border)] bg-[var(--color-success-subtle)] px-4 py-3 text-sm text-[var(--color-text-primary)]" role="status"><?php esc_html_e('Your profile has been updated.', 'wordpress-demo-theme'); ?></div>
<?php elseif($profile_error) : ?>
    <div class="mt-6 rounded-xl border border-[var(--color-error-border)] bg-[var(--color-error-subtle)] px-4 py-3 text-sm text-[var(--color-error)]" role="alert"><?php echo esc_html($profile_errors[$profile_error] ?? $profile_errors['update_failed']); ?></div>
<?php endif; ?>

<form method="post" action="<?php echo esc_url(home_url('/dashboard/profile/')); ?>" class="mt-6 rounded-2xl border border-[var(--color-border)] bg-[var(--color-card)] p-5 shadow-[var(--shadow-sm)] sm:p-6">
    <?php wp_nonce_field('wordpress_demo_theme_update_profile', 'wordpress_demo_theme_profile_nonce'); ?>

    <div class="flex flex-col gap-4 border-b border-[var(--color-border)] pb-6 sm:flex-row sm:items-center">
        <?php echo get_avatar($current_user->ID, 80, '', $current_user->display_name, array('class' => 'size-20 rounded-full object-cover')); ?>
        <div>
            <strong class="block text-base text-[var(--color-text-primary)]"><?php echo esc_html($current_user->display_name); ?></strong>
            <p class="mt-1 text-sm text-[var(--color-text-muted)]"><?php esc_html_e('Your profile image is provided by Gravatar.', 'wordpress-demo-theme'); ?></p>
        </div>
    </div>

    <div class="mt-6 grid gap-5 sm:grid-cols-2">
        <div>
            <label for="first_name" class="mb-2 block text-sm font-medium text-[var(--color-text-primary)]"><?php esc_html_e('First name', 'wordpress-demo-theme'); ?></label>
            <input id="first_name" name="first_name" type="text" value="<?php echo esc_attr($current_user->first_name); ?>" autocomplete="given-name" class="h-11 w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-input)] px-4 text-sm outline-none focus:border-[var(--color-primary)] focus:ring-4 focus:ring-[var(--color-focus-ring)]">
        </div>
        <div>
            <label for="last_name" class="mb-2 block text-sm font-medium text-[var(--color-text-primary)]"><?php esc_html_e('Last name', 'wordpress-demo-theme'); ?></label>
            <input id="last_name" name="last_name" type="text" value="<?php echo esc_attr($current_user->last_name); ?>" autocomplete="family-name" class="h-11 w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-input)] px-4 text-sm outline-none focus:border-[var(--color-primary)] focus:ring-4 focus:ring-[var(--color-focus-ring)]">
        </div>
        <div>
            <label for="display_name" class="mb-2 block text-sm font-medium text-[var(--color-text-primary)]"><?php esc_html_e('Display name', 'wordpress-demo-theme'); ?></label>
            <input id="display_name" name="display_name" type="text" required value="<?php echo esc_attr($current_user->display_name); ?>" class="h-11 w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-input)] px-4 text-sm outline-none focus:border-[var(--color-primary)] focus:ring-4 focus:ring-[var(--color-focus-ring)]">
        </div>
        <div>
            <label for="email" class="mb-2 block text-sm font-medium text-[var(--color-text-primary)]"><?php esc_html_e('Email address', 'wordpress-demo-theme'); ?></label>
            <input id="email" name="email" type="email" required value="<?php echo esc_attr($current_user->user_email); ?>" autocomplete="email" class="h-11 w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-input)] px-4 text-sm outline-none focus:border-[var(--color-primary)] focus:ring-4 focus:ring-[var(--color-focus-ring)]">
        </div>
        <div class="sm:col-span-2">
            <label for="user_url" class="mb-2 block text-sm font-medium text-[var(--color-text-primary)]"><?php esc_html_e('Website', 'wordpress-demo-theme'); ?></label>
            <input id="user_url" name="user_url" type="url" value="<?php echo esc_attr($current_user->user_url); ?>" placeholder="https://" class="h-11 w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-input)] px-4 text-sm outline-none focus:border-[var(--color-primary)] focus:ring-4 focus:ring-[var(--color-focus-ring)]">
        </div>
        <div class="sm:col-span-2">
            <label for="description" class="mb-2 block text-sm font-medium text-[var(--color-text-primary)]"><?php esc_html_e('Biography', 'wordpress-demo-theme'); ?></label>
            <textarea id="description" name="description" rows="5" class="w-full resize-y rounded-xl border border-[var(--color-border)] bg-[var(--color-input)] px-4 py-3 text-sm leading-6 outline-none focus:border-[var(--color-primary)] focus:ring-4 focus:ring-[var(--color-focus-ring)]"><?php echo esc_textarea($current_user->description); ?></textarea>
        </div>
    </div>

    <div class="mt-6 flex justify-end">
        <button type="submit" class="inline-flex h-11 items-center justify-center rounded-full bg-[var(--color-primary)] px-6 text-sm font-semibold text-[var(--color-primary-text)] transition hover:bg-[var(--color-primary-hover)] focus:outline-none focus:ring-4 focus:ring-[var(--color-focus-ring)]"><?php esc_html_e('Save profile', 'wordpress-demo-theme'); ?></button>
    </div>
</form>
