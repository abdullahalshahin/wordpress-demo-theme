<?php
/**
 * User: Authentication
 *
 * Provides frontend user authentication integrations.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

if(!function_exists('wordpress_demo_theme_is_member')) {
    function wordpress_demo_theme_is_member($user = null) {
        $user = $user instanceof WP_User ? $user : wp_get_current_user();

        return $user->exists() && !user_can($user, 'edit_posts');
    }
}

if(!function_exists('wordpress_demo_theme_set_member_toolbar_preference')) {
    function wordpress_demo_theme_set_member_toolbar_preference() {
        if(is_admin() || !is_user_logged_in() || !wordpress_demo_theme_is_member()) {
            return;
        }

        $user_id = get_current_user_id();

        if(get_user_meta($user_id, 'show_admin_bar_front', true) !== 'false') {
            update_user_meta($user_id, 'show_admin_bar_front', 'false');
        }
    }
}

add_action('init', 'wordpress_demo_theme_set_member_toolbar_preference', 20);

if(!function_exists('wordpress_demo_theme_restrict_member_admin')) {
    function wordpress_demo_theme_restrict_member_admin() {
        global $pagenow;

        if(wordpress_demo_theme_is_member() && !wp_doing_ajax() && $pagenow !== 'admin-post.php') {
            wp_safe_redirect(home_url('/dashboard/'));
            exit;
        }
    }
}

add_action('admin_init', 'wordpress_demo_theme_restrict_member_admin');

if(!function_exists('wordpress_demo_theme_login_url')) {
    function wordpress_demo_theme_login_url($redirect_url = '') {
        $login_url = home_url('/login/');
        $redirect_url = $redirect_url ? wp_validate_redirect($redirect_url, '') : '';

        return $redirect_url ? add_query_arg('redirect_to', $redirect_url, $login_url) : $login_url;
    }
}

if(!function_exists('wordpress_demo_theme_is_login_page')) {
    function wordpress_demo_theme_is_login_page() {
        if(is_admin()) {
            return false;
        }

        $request_path = isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI']) ? wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH) : '';
        $login_path = wp_parse_url(home_url('/login/'), PHP_URL_PATH);

        return untrailingslashit((string) $request_path) === untrailingslashit((string) $login_path);
    }
}

if(!function_exists('wordpress_demo_theme_is_registration_page')) {
    function wordpress_demo_theme_is_registration_page() {
        if(is_admin()) {
            return false;
        }

        $request_path = isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI']) ? wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH) : '';
        $registration_path = wp_parse_url(home_url('/registration/'), PHP_URL_PATH);

        return untrailingslashit((string) $request_path) === untrailingslashit((string) $registration_path);
    }
}

if(!function_exists('wordpress_demo_theme_get_dashboard_section')) {
    function wordpress_demo_theme_get_dashboard_section() {
        if(is_admin()) {
            return '';
        }

        $request_path = isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI']) ? wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH) : '';
        $dashboard_path = untrailingslashit((string) wp_parse_url(home_url('/dashboard/'), PHP_URL_PATH));
        $request_path = untrailingslashit((string) $request_path);

        if($request_path === $dashboard_path) {
            return 'dashboard';
        }

        if(strpos($request_path, $dashboard_path . '/') !== 0) {
            return '';
        }

        $section = sanitize_key(substr($request_path, strlen($dashboard_path) + 1));

        return in_array($section, array('profile'), true) ? $section : '';
    }
}

if(!function_exists('wordpress_demo_theme_login_template')) {
    function wordpress_demo_theme_login_template($template) {
        if(wordpress_demo_theme_is_login_page()) {
            status_header(200);
            nocache_headers();

            return WORDPRESS_DEMO_THEME_PATH . 'page-login.php';
        }

        if(wordpress_demo_theme_is_registration_page()) {
            status_header(200);
            nocache_headers();

            return WORDPRESS_DEMO_THEME_PATH . 'page-registration.php';
        }

        if(wordpress_demo_theme_get_dashboard_section()) {
            status_header(200);
            nocache_headers();

            return WORDPRESS_DEMO_THEME_PATH . 'inc/user/pages/dashboard.php';
        }

        return $template;
    }
}

if(!function_exists('wordpress_demo_theme_handle_dashboard')) {
    function wordpress_demo_theme_handle_dashboard() {
        $dashboard_section = wordpress_demo_theme_get_dashboard_section();

        if(!$dashboard_section) {
            return;
        }

        global $wp_query;

        if($wp_query) {
            $wp_query->is_404 = false;
        }

        status_header(200);
        nocache_headers();

        if(!is_user_logged_in()) {
            $login_url = add_query_arg('redirect_to', home_url('/dashboard/' . ($dashboard_section === 'profile' ? 'profile/' : '')), home_url('/login/'));
            wp_safe_redirect($login_url);
            exit;
        }

        $request_method = isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';

        if($dashboard_section !== 'profile' || $request_method !== 'POST') {
            return;
        }

        $nonce = isset($_POST['wordpress_demo_theme_profile_nonce']) && is_string($_POST['wordpress_demo_theme_profile_nonce']) ? sanitize_text_field(wp_unslash($_POST['wordpress_demo_theme_profile_nonce'])) : '';

        if(!wp_verify_nonce($nonce, 'wordpress_demo_theme_update_profile')) {
            wp_safe_redirect(add_query_arg('profile_error', 'security', home_url('/dashboard/profile/')));
            exit;
        }

        $user_id = get_current_user_id();
        $email = isset($_POST['email']) && is_string($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $display_name = isset($_POST['display_name']) && is_string($_POST['display_name']) ? sanitize_text_field(wp_unslash($_POST['display_name'])) : '';
        $existing_email_user = $email ? email_exists($email) : false;

        if(!is_email($email) || $display_name === '') {
            $error = 'invalid_details';
        }
        elseif($existing_email_user && absint($existing_email_user) !== $user_id) {
            $error = 'email_exists';
        }
        else {
            $error = '';
        }

        if($error !== '') {
            wp_safe_redirect(add_query_arg('profile_error', $error, home_url('/dashboard/profile/')));
            exit;
        }

        $updated_user = wp_update_user(array(
            'ID' => $user_id,
            'first_name' => isset($_POST['first_name']) && is_string($_POST['first_name']) ? sanitize_text_field(wp_unslash($_POST['first_name'])) : '',
            'last_name' => isset($_POST['last_name']) && is_string($_POST['last_name']) ? sanitize_text_field(wp_unslash($_POST['last_name'])) : '',
            'display_name' => $display_name,
            'user_email' => $email,
            'user_url' => isset($_POST['user_url']) && is_string($_POST['user_url']) ? esc_url_raw(wp_unslash($_POST['user_url'])) : '',
            'description' => isset($_POST['description']) && is_string($_POST['description']) ? sanitize_textarea_field(wp_unslash($_POST['description'])) : ''
        ));

        if(is_wp_error($updated_user)) {
            wp_safe_redirect(add_query_arg('profile_error', 'update_failed', home_url('/dashboard/profile/')));
            exit;
        }

        wp_safe_redirect(add_query_arg('profile_updated', '1', home_url('/dashboard/profile/')));
        exit;
    }
}

if(!function_exists('wordpress_demo_theme_create_username')) {
    function wordpress_demo_theme_create_username($email) {
        $email_parts = explode('@', $email);
        $base_username = sanitize_user($email_parts[0], true);
        $base_username = $base_username !== '' ? $base_username : 'member';
        $username = $base_username;
        $suffix = 1;

        while(username_exists($username)) {
            $username = $base_username . $suffix;
            $suffix++;
        }

        return $username;
    }
}

if(!function_exists('wordpress_demo_theme_handle_registration')) {
    function wordpress_demo_theme_handle_registration() {
        if(!wordpress_demo_theme_is_registration_page()) {
            return;
        }

        global $wp_query;

        if($wp_query) {
            $wp_query->is_404 = false;
        }

        status_header(200);
        nocache_headers();

        if(is_user_logged_in()) {
            wp_safe_redirect(home_url('/dashboard/'));
            exit;
        }

        $request_method = isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';

        if($request_method !== 'POST') {
            return;
        }

        $nonce = isset($_POST['wordpress_demo_theme_registration_nonce']) && is_string($_POST['wordpress_demo_theme_registration_nonce']) ? sanitize_text_field(wp_unslash($_POST['wordpress_demo_theme_registration_nonce'])) : '';

        if(!wp_verify_nonce($nonce, 'wordpress_demo_theme_registration')) {
            wp_safe_redirect(add_query_arg('registration_error', 'security', home_url('/registration/')));
            exit;
        }

        $first_name = isset($_POST['first_name']) && is_string($_POST['first_name']) ? sanitize_text_field(wp_unslash($_POST['first_name'])) : '';
        $last_name = isset($_POST['last_name']) && is_string($_POST['last_name']) ? sanitize_text_field(wp_unslash($_POST['last_name'])) : '';
        $email = isset($_POST['email']) && is_string($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $password = isset($_POST['password']) && is_string($_POST['password']) ? wp_unslash($_POST['password']) : '';
        $confirm_password = isset($_POST['confirm_password']) && is_string($_POST['confirm_password']) ? wp_unslash($_POST['confirm_password']) : '';

        if($first_name === '' || $last_name === '' || !is_email($email)) {
            $error = 'invalid_details';
        }
        elseif(email_exists($email)) {
            $error = 'email_exists';
        }
        elseif(strlen($password) < 6) {
            $error = 'weak_password';
        }
        elseif($password !== $confirm_password) {
            $error = 'password_mismatch';
        }
        elseif(empty($_POST['accept_terms'])) {
            $error = 'terms_required';
        }
        else {
            $error = '';
        }

        if($error !== '') {
            wp_safe_redirect(add_query_arg('registration_error', $error, home_url('/registration/')));
            exit;
        }

        $display_name = trim($first_name . ' ' . $last_name);
        $user_id = wp_insert_user(array(
            'user_login' => wordpress_demo_theme_create_username($email),
            'user_pass' => $password,
            'user_email' => $email,
            'display_name' => $display_name,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'role' => 'subscriber'
        ));

        if(is_wp_error($user_id)) {
            wp_safe_redirect(add_query_arg('registration_error', 'create_failed', home_url('/registration/')));
            exit;
        }

        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true, is_ssl());
        do_action('wp_login', get_userdata($user_id)->user_login, get_userdata($user_id));
        wp_safe_redirect(home_url('/dashboard/'));
        exit;
    }
}

if(!function_exists('wordpress_demo_theme_handle_login')) {
    function wordpress_demo_theme_handle_login() {
        if(!wordpress_demo_theme_is_login_page()) {
            return;
        }

        global $wp_query;

        if($wp_query) {
            $wp_query->is_404 = false;
        }

        status_header(200);
        nocache_headers();

        if(is_user_logged_in()) {
            wp_safe_redirect(home_url('/dashboard/'));
            exit;
        }

        $request_method = isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';

        if($request_method !== 'POST') {
            return;
        }

        $nonce = isset($_POST['wordpress_demo_theme_login_nonce']) && is_string($_POST['wordpress_demo_theme_login_nonce']) ? sanitize_text_field(wp_unslash($_POST['wordpress_demo_theme_login_nonce'])) : '';

        if(!wp_verify_nonce($nonce, 'wordpress_demo_theme_login')) {
            wp_safe_redirect(add_query_arg('login_error', 'security', home_url('/login/')));
            exit;
        }

        $user_login = isset($_POST['user_login']) && is_string($_POST['user_login']) ? sanitize_text_field(wp_unslash($_POST['user_login'])) : '';
        $user_password = isset($_POST['user_password']) && is_string($_POST['user_password']) ? wp_unslash($_POST['user_password']) : '';
        $remember = !empty($_POST['remember']);
        $user = wp_signon(array(
            'user_login' => $user_login,
            'user_password' => $user_password,
            'remember' => $remember
        ), is_ssl());

        if(is_wp_error($user)) {
            wp_safe_redirect(add_query_arg('login_error', 'invalid', home_url('/login/')));
            exit;
        }

        $redirect_to = isset($_POST['redirect_to']) && is_string($_POST['redirect_to']) ? wp_validate_redirect(wp_unslash($_POST['redirect_to']), home_url('/dashboard/')) : home_url('/dashboard/');
        wp_safe_redirect($redirect_to);
        exit;
    }
}

if(!function_exists('wordpress_demo_theme_login_document_title')) {
    function wordpress_demo_theme_login_document_title($title) {
        if(wordpress_demo_theme_is_login_page()) {
            $title['title'] = __('Sign in', 'wordpress-demo-theme');
        }
        elseif(wordpress_demo_theme_is_registration_page()) {
            $title['title'] = __('Create account', 'wordpress-demo-theme');
        }
        elseif(wordpress_demo_theme_get_dashboard_section() === 'profile') {
            $title['title'] = __('Profile', 'wordpress-demo-theme');
        }
        elseif(wordpress_demo_theme_get_dashboard_section() === 'dashboard') {
            $title['title'] = __('Dashboard', 'wordpress-demo-theme');
        }

        return $title;
    }
}

add_action('template_redirect', 'wordpress_demo_theme_handle_login', 1);
add_action('template_redirect', 'wordpress_demo_theme_handle_registration', 1);
add_action('template_redirect', 'wordpress_demo_theme_handle_dashboard', 1);
add_filter('template_include', 'wordpress_demo_theme_login_template');
add_filter('document_title_parts', 'wordpress_demo_theme_login_document_title');
