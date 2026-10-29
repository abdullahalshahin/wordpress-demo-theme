<?php
/**
 * Theme: Helper Functions
 *
 * Provides reusable utility functions for theme components,
 * layouts, and pages.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

if(!function_exists('wordpress_demo_theme_get_site_name')) {
    function wordpress_demo_theme_get_site_name() {
        return get_bloginfo('name');
    }
}

if(!function_exists('wordpress_demo_theme_logo')) {
    function wordpress_demo_theme_logo() {
        $site_name = wordpress_demo_theme_get_site_name();

        $admin_logo_id = absint(get_theme_mod('custom_logo', 0));
        $admin_dark_logo_id = absint(get_theme_mod('wordpress_demo_theme_dark_logo', 0));

        $admin_logo = $admin_logo_id ? wp_get_attachment_image_url($admin_logo_id, 'full') : '';
        $admin_dark_logo = $admin_dark_logo_id ? wp_get_attachment_image_url($admin_dark_logo_id, 'full') : '';

        $default_logo = WORDPRESS_DEMO_THEME_URI . 'assets/images/logo.png';
        $default_dark_logo = WORDPRESS_DEMO_THEME_URI . 'assets/images/logo-light.png';

        if($admin_logo || $admin_dark_logo) {
            $light_logo = $admin_logo ?: $admin_dark_logo;
            $dark_logo = $admin_dark_logo ?: $admin_logo;
        }
        else {
            $light_logo = $default_logo;
            $dark_logo = $default_dark_logo;
        }

        if($light_logo === $dark_logo) {
            echo '<img src="' . esc_url($light_logo) . '" alt="' . esc_attr($site_name) . '" class="block h-auto max-h-10 w-auto max-w-44 object-contain">';
            return;
        }

        echo '<img src="' . esc_url($light_logo) . '" alt="' . esc_attr($site_name) . '" class="block h-auto max-h-10 w-auto max-w-44 object-contain [[data-theme=dark]_&]:hidden">';
        echo '<img src="' . esc_url($dark_logo) . '" alt="' . esc_attr($site_name) . '" class="hidden h-auto max-h-10 w-auto max-w-44 object-contain [[data-theme=dark]_&]:block">';
    }
}

if(!function_exists('wordpress_demo_theme_website_header')) {
    function wordpress_demo_theme_website_header() {
        require WORDPRESS_DEMO_THEME_PATH . 'inc/components/website-header.php';
    }
}

if(!function_exists('wordpress_demo_theme_breadcrumb')) {
    function wordpress_demo_theme_breadcrumb($args = array()) {
        $args = wp_parse_args($args, array(
            'items' => null
        ));

        $breadcrumb_items = is_array($args['items']) ? $args['items'] : wordpress_demo_theme_get_breadcrumb_items();

        require WORDPRESS_DEMO_THEME_PATH . 'inc/components/breadcrumb.php';
    }
}

if(!function_exists('wordpress_demo_theme_blog_card')) {
    function wordpress_demo_theme_blog_card($post_id = 0) {
        $post_id = absint($post_id ?: get_the_ID());

        require WORDPRESS_DEMO_THEME_PATH . 'inc/components/blog-card.php';
    }
}

if(!function_exists('wordpress_demo_theme_get_copyright')) {
    function wordpress_demo_theme_get_copyright() {
        $company_name = get_theme_mod('wordpress_demo_theme_company_name', __('Example Company', 'wordpress-demo-theme'));
        $company_url = get_theme_mod('wordpress_demo_theme_company_url', 'https://example.com');

        $company_link = '<a href="' . esc_url($company_url) . '" class="font-medium transition-colors hover:text-[var(--color-primary)]" target="_blank" rel="noopener noreferrer">' . esc_html($company_name) . '</a>';

        return '© ' . esc_html(wp_date('Y')) . ' ' . $company_link . '. ' . esc_html__('All rights reserved.', 'wordpress-demo-theme');
    }
}

if(!function_exists('wordpress_demo_theme_get_available_designs')) {
    function wordpress_demo_theme_get_available_designs($component) {
        $designs = array(
            'header' => array(
                'design-1' => __('Design 1', 'wordpress-demo-theme'),
                'design-2' => __('Design 2', 'wordpress-demo-theme'),
                'design-3' => __('Design 3', 'wordpress-demo-theme'),
            ),
            'breadcrumb' => array(
                'design-1' => __('Design 1', 'wordpress-demo-theme'),
                'design-2' => __('Design 2', 'wordpress-demo-theme'),
                'design-3' => __('Design 3', 'wordpress-demo-theme'),
            ),
            'blog-card' => array(
                'design-1' => __('Design 1', 'wordpress-demo-theme'),
                'design-2' => __('Design 2', 'wordpress-demo-theme'),
            ),
        );

        return isset($designs[$component]) ? $designs[$component] : array();
    }
}

if(!function_exists('wordpress_demo_theme_popular_categories')) {
    function wordpress_demo_theme_popular_categories($limit = 10) {
        $limit = absint($limit);

        if($limit === 0) {
            return array();
        }

        return get_categories(array(
            'taxonomy' => 'category',
            'orderby' => 'count',
            'order' => 'DESC',
            'number' => $limit,
            'hide_empty' => true
        ));
    }
}

if(!function_exists('wordpress_demo_theme_reading_time')) {
    function wordpress_demo_theme_reading_time($post_id = 0) {
        $post_id = $post_id ?: get_the_ID();

        if(!$post_id) {
            return 0;
        }

        $post_content = get_post_field('post_content', $post_id, 'raw');

        if(!is_string($post_content) || trim($post_content) === '') {
            return 0;
        }

        $plain_text = wp_strip_all_tags(strip_shortcodes($post_content));
        $word_count = preg_match_all('/[\p{L}\p{N}]+(?:[\x{27}\x{2019}][\p{L}\p{N}]+)*/u', $plain_text);

        if(!$word_count) {
            return 0;
        }

        return max(1, (int) ceil($word_count / 200));
    }
}

if(!function_exists('wordpress_demo_theme_get_post_categories')) {
    function wordpress_demo_theme_get_post_categories($post_id = 0) {
        $post_id = $post_id ?: get_the_ID();

        if(!$post_id) {
            return array();
        }

        $categories = get_the_category($post_id);

        if(empty($categories) || is_wp_error($categories)) {
            return array();
        }

        return $categories;
    }
}

if(!function_exists('wordpress_demo_theme_format_compact_number')) {
    function wordpress_demo_theme_format_compact_number($number) {
        $number = max(0, (int) $number);

        if($number < 1000) {
            return number_format_i18n($number);
        }

        $divisor = $number >= 1000000 ? 1000000 : 1000;
        $suffix = $number >= 1000000 ? 'M' : 'K';
        $value = $number / $divisor;
        $decimals = $value < 10 && abs($value - round($value)) > 0.0001 ? 1 : 0;

        return number_format_i18n($value, $decimals) . $suffix;
    }
}

if(!function_exists('wordpress_demo_theme_get_author_name')) {
    function wordpress_demo_theme_get_author_name($post_id = 0, $author_id = 0) {
        $author_id = absint($author_id);

        if(!$author_id) {
            $post_id = absint($post_id ?: get_the_ID());

            if(!$post_id) {
                return '';
            }

            $author_id = absint(get_post_field('post_author', $post_id));
        }

        if(!$author_id) {
            return '';
        }

        $author_first_name = get_the_author_meta('first_name', $author_id);
        $author_last_name = get_the_author_meta('last_name', $author_id);
        $author_name = trim($author_first_name . ' ' . $author_last_name);

        if($author_name === '') {
            $author_name = get_the_author_meta('display_name', $author_id);
        }

        return $author_name;
    }
}

if(!function_exists('wordpress_demo_theme_get_author_profile_picture')) {
    function wordpress_demo_theme_get_author_profile_picture($post_id = 0, $size = 88) {
        $post_id = absint($post_id ?: get_the_ID());
        $size = max(1, absint($size));

        if(!$post_id) {
            return get_avatar_url(0, array('size' => $size, 'default' => 'mystery'));
        }

        $author_id = absint(get_post_field('post_author', $post_id));

        if(!$author_id) {
            return get_avatar_url(0, array('size' => $size, 'default' => 'mystery'));
        }

        return get_avatar_url($author_id, array(
            'size' => $size,
            'default' => 'mystery'
        ));
    }
}

if(!function_exists('wordpress_demo_theme_get_post_interactions')) {
    function wordpress_demo_theme_get_post_interactions($post_id) {
        global $wpdb;

        $post_id = absint($post_id);

        if(!$post_id || get_post_type($post_id) !== 'post' || get_post_status($post_id) !== 'publish') {
            return array(
                'like_count' => 0,
                'liked' => false,
                'saved' => false
            );
        }

        $like_key = '_wordpress_demo_theme_liked_post_' . $post_id;
        $save_key = '_wordpress_demo_theme_saved_post_' . $post_id;

        $like_count = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value <> ''",
            $like_key
        ));

        $liked = false;
        $saved = false;

        if(is_user_logged_in()) {
            $user_id = get_current_user_id();

            $liked = (bool) get_user_meta($user_id, $like_key, true);
            $saved = (bool) get_user_meta($user_id, $save_key, true);
        }

        return array(
            'like_count' => max(0, $like_count),
            'liked' => $liked,
            'saved' => $saved
        );
    }
}

if(!function_exists('wordpress_demo_theme_get_post_interaction_counts')) {
    function wordpress_demo_theme_get_post_interaction_counts($post_ids, $interaction) {
        global $wpdb;

        $post_ids = array_values(array_unique(array_filter(array_map('absint', (array) $post_ids))));
        $interaction = in_array($interaction, array('like', 'save'), true) ? $interaction : '';
        $counts = array_fill_keys($post_ids, 0);

        if(empty($post_ids) || $interaction === '') {
            return $counts;
        }

        $meta_prefix = '_wordpress_demo_theme_' . ($interaction === 'like' ? 'liked' : 'saved') . '_post_';
        $meta_keys = array_map(function($post_id) use ($meta_prefix) {
            return $meta_prefix . $post_id;
        }, $post_ids);
        $placeholders = implode(', ', array_fill(0, count($meta_keys), '%s'));
        $query = "SELECT meta_key, COUNT(DISTINCT user_id) AS total FROM {$wpdb->usermeta} WHERE meta_key IN ({$placeholders}) AND meta_value <> '' GROUP BY meta_key";
        $rows = $wpdb->get_results($wpdb->prepare($query, $meta_keys));

        foreach($rows as $row) {
            $post_id = absint(substr($row->meta_key, strlen($meta_prefix)));

            if(isset($counts[$post_id])) {
                $counts[$post_id] = (int) $row->total;
            }
        }

        return $counts;
    }
}

if(!function_exists('wordpress_demo_theme_get_post_interaction_users')) {
    function wordpress_demo_theme_get_post_interaction_users($post_id, $interaction, $limit = 20, $offset = 0) {
        global $wpdb;

        $post_id = absint($post_id);
        $interaction = in_array($interaction, array('like', 'save'), true) ? $interaction : '';
        $limit = max(1, min(50, absint($limit)));
        $offset = absint($offset);

        if(!$post_id || $interaction === '') {
            return array();
        }

        $meta_key = '_wordpress_demo_theme_' . ($interaction === 'like' ? 'liked' : 'saved') . '_post_' . $post_id;

        return $wpdb->get_results($wpdb->prepare(
            "SELECT interactions.user_id, MAX(interactions.meta_value) AS meta_value, users.display_name, MAX(first_names.meta_value) AS first_name, MAX(last_names.meta_value) AS last_name FROM {$wpdb->usermeta} AS interactions INNER JOIN {$wpdb->users} AS users ON users.ID = interactions.user_id LEFT JOIN {$wpdb->usermeta} AS first_names ON first_names.user_id = interactions.user_id AND first_names.meta_key = 'first_name' LEFT JOIN {$wpdb->usermeta} AS last_names ON last_names.user_id = interactions.user_id AND last_names.meta_key = 'last_name' WHERE interactions.meta_key = %s AND interactions.meta_value <> '' GROUP BY interactions.user_id, users.display_name ORDER BY CAST(MAX(interactions.meta_value) AS UNSIGNED) DESC, interactions.user_id DESC LIMIT %d OFFSET %d",
            $meta_key,
            $limit,
            $offset
        ));
    }
}

if(!function_exists('wordpress_demo_theme_get_user_name_from_record')) {
    function wordpress_demo_theme_get_user_name_from_record($user) {
        $first_name = isset($user->first_name) ? trim((string) $user->first_name) : '';
        $last_name = isset($user->last_name) ? trim((string) $user->last_name) : '';
        $full_name = trim($first_name . ' ' . $last_name);

        return $full_name !== '' ? $full_name : (string) $user->display_name;
    }
}

if(!function_exists('wordpress_demo_theme_get_comment_reaction_counts')) {
    function wordpress_demo_theme_get_comment_reaction_counts($comment_ids) {
        global $wpdb;

        $comment_ids = array_values(array_unique(array_filter(array_map('absint', (array) $comment_ids))));
        $counts = array_fill_keys($comment_ids, array('like' => 0, 'dislike' => 0));

        if(empty($comment_ids)) {
            return $counts;
        }

        $placeholders = implode(', ', array_fill(0, count($comment_ids), '%d'));
        $meta_pattern = $wpdb->esc_like('_wordpress_demo_theme_comment_reaction_') . '%';
        $query = "SELECT comment_id, meta_value, COUNT(DISTINCT meta_key) AS total FROM {$wpdb->commentmeta} WHERE comment_id IN ({$placeholders}) AND meta_key LIKE %s AND meta_value IN ('like', 'dislike') GROUP BY comment_id, meta_value";
        $rows = $wpdb->get_results($wpdb->prepare($query, array_merge($comment_ids, array($meta_pattern))));

        foreach($rows as $row) {
            $comment_id = absint($row->comment_id);

            if(isset($counts[$comment_id]) && isset($counts[$comment_id][$row->meta_value])) {
                $counts[$comment_id][$row->meta_value] = (int) $row->total;
            }
        }

        return $counts;
    }
}

if(!function_exists('wordpress_demo_theme_get_comment_reaction_users')) {
    function wordpress_demo_theme_get_comment_reaction_users($comment_id, $reaction, $limit = 20, $offset = 0) {
        global $wpdb;

        $comment_id = absint($comment_id);
        $reaction = in_array($reaction, array('like', 'dislike'), true) ? $reaction : '';
        $limit = max(1, min(50, absint($limit)));
        $offset = absint($offset);

        if(!$comment_id || $reaction === '') {
            return array();
        }

        $reaction_prefix = '_wordpress_demo_theme_comment_reaction_';
        $time_prefix = '_wordpress_demo_theme_comment_reaction_at_';
        $reaction_pattern = $wpdb->esc_like($reaction_prefix) . '%';
        $user_id_start = strlen($reaction_prefix) + 1;

        return $wpdb->get_results($wpdb->prepare(
            "SELECT users.ID AS user_id, users.display_name, MAX(first_names.meta_value) AS first_name, MAX(last_names.meta_value) AS last_name, MAX(times.meta_value) AS reacted_at FROM {$wpdb->commentmeta} AS reactions INNER JOIN {$wpdb->users} AS users ON users.ID = CAST(SUBSTRING(reactions.meta_key, %d) AS UNSIGNED) LEFT JOIN {$wpdb->usermeta} AS first_names ON first_names.user_id = users.ID AND first_names.meta_key = 'first_name' LEFT JOIN {$wpdb->usermeta} AS last_names ON last_names.user_id = users.ID AND last_names.meta_key = 'last_name' LEFT JOIN {$wpdb->commentmeta} AS times ON times.comment_id = reactions.comment_id AND times.meta_key = CONCAT(%s, users.ID) WHERE reactions.comment_id = %d AND reactions.meta_key LIKE %s AND reactions.meta_value = %s GROUP BY users.ID, users.display_name ORDER BY CAST(MAX(times.meta_value) AS UNSIGNED) DESC, users.ID DESC LIMIT %d OFFSET %d",
            $user_id_start,
            $time_prefix,
            $comment_id,
            $reaction_pattern,
            $reaction,
            $limit,
            $offset
        ));
    }
}
