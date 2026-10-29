<?php
/**
 * Core: Theme Hooks
 *
 * Manages global WordPress actions and filters
 * for theme customization and functionality.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

if(!function_exists('wordpress_demo_theme_excerpt_length')) {
    function wordpress_demo_theme_excerpt_length($length) {
        if(is_admin()) {
            return $length;
        }

        return 25;
    }
}

add_filter('excerpt_length', 'wordpress_demo_theme_excerpt_length', 999);

if(!function_exists('wordpress_demo_theme_excerpt_more')) {
    function wordpress_demo_theme_excerpt_more($more) {
        return '...';
    }
}

add_filter('excerpt_more', 'wordpress_demo_theme_excerpt_more');

if(!function_exists('wordpress_demo_theme_body_classes')) {
    function wordpress_demo_theme_body_classes($classes) {
        if(is_front_page()) {
            $classes[] = 'is-front-page';
        }

        if(is_singular()) {
            $classes[] = 'is-singular';
        }

        if(is_archive()) {
            $classes[] = 'is-archive';
        }

        return $classes;
    }
}

add_filter('body_class', 'wordpress_demo_theme_body_classes');

if(!function_exists('wordpress_demo_theme_handle_post_interaction')) {
    function wordpress_demo_theme_handle_post_interaction() {
        if(!is_user_logged_in()) {
            $post_id = isset($_POST['post_id']) && is_scalar($_POST['post_id']) ? absint($_POST['post_id']) : 0;

            $redirect_url = $post_id && get_post_type($post_id) === 'post' && get_post_status($post_id) === 'publish'
                ? get_permalink($post_id)
                : home_url('/');

            wp_send_json_error(array(
                'message' => __('Please log in to continue.', 'wordpress-demo-theme'),
                'login_url' => wordpress_demo_theme_login_url($redirect_url)
            ), 401);
        }

        if(!check_ajax_referer('wordpress_demo_theme_post_interaction', 'nonce', false)) {
            wp_send_json_error(array(
                'message' => __('Invalid security token. Please refresh the page.', 'wordpress-demo-theme')
            ), 403);
        }

        $post_id = isset($_POST['post_id']) && is_scalar($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        $interaction = isset($_POST['interaction']) && is_string($_POST['interaction']) ? sanitize_key(wp_unslash($_POST['interaction'])) : '';

        if(!$post_id || get_post_type($post_id) !== 'post' || get_post_status($post_id) !== 'publish') {
            wp_send_json_error(array(
                'message' => __('Invalid post.', 'wordpress-demo-theme')
            ), 400);
        }

        if(post_password_required($post_id)) {
            wp_send_json_error(array(
                'message' => __('This post is password protected.', 'wordpress-demo-theme')
            ), 403);
        }

        if(!in_array($interaction, array('like', 'save'), true)) {
            wp_send_json_error(array(
                'message' => __('Invalid interaction.', 'wordpress-demo-theme')
            ), 400);
        }

        $user_id = get_current_user_id();
        $meta_key = '_wordpress_demo_theme_' . ($interaction === 'like' ? 'liked' : 'saved') . '_post_' . $post_id;
        $is_active = (bool) get_user_meta($user_id, $meta_key, true);

        if($is_active) {
            $updated = delete_user_meta($user_id, $meta_key);
        }
        else {
            $updated = update_user_meta($user_id, $meta_key, (string) time());
        }

        if(!$updated) {
            wp_send_json_error(array(
                'message' => __('Could not update this post. Please try again.', 'wordpress-demo-theme')
            ), 500);
        }

        $post_interactions = wordpress_demo_theme_get_post_interactions($post_id);

        wp_send_json_success(array(
            'like_count' => $post_interactions['like_count'],
            'liked' => $post_interactions['liked'],
            'saved' => $post_interactions['saved']
        ));
    }
}

add_action('wp_ajax_wordpress_demo_theme_post_interaction', 'wordpress_demo_theme_handle_post_interaction');
add_action('wp_ajax_nopriv_wordpress_demo_theme_post_interaction', 'wordpress_demo_theme_handle_post_interaction');

if(!function_exists('wordpress_demo_theme_post_columns')) {
    function wordpress_demo_theme_post_columns($columns) {
        $updated_columns = array();
        $interaction_columns_added = false;

        foreach($columns as $column_key => $column_label) {
            if($column_key === 'comments') {
                $updated_columns['wordpress_demo_theme_likes'] = __('Likes', 'wordpress-demo-theme');
                $updated_columns['wordpress_demo_theme_saves'] = __('Saved', 'wordpress-demo-theme');
                $interaction_columns_added = true;
            }

            $updated_columns[$column_key] = $column_label;
        }

        if(!$interaction_columns_added) {
            $updated_columns['wordpress_demo_theme_likes'] = __('Likes', 'wordpress-demo-theme');
            $updated_columns['wordpress_demo_theme_saves'] = __('Saved', 'wordpress-demo-theme');
        }

        return $updated_columns;
    }
}

if(!function_exists('wordpress_demo_theme_post_interaction_column')) {
    function wordpress_demo_theme_post_interaction_column($column, $post_id) {
        static $interaction_counts = array('like' => null, 'save' => null);

        $interaction = $column === 'wordpress_demo_theme_likes' ? 'like' : ($column === 'wordpress_demo_theme_saves' ? 'save' : '');

        if($interaction === '') {
            return;
        }

        if($interaction_counts[$interaction] === null) {
            global $wp_query;

            $visible_post_ids = !empty($wp_query->posts) ? wp_list_pluck($wp_query->posts, 'ID') : array($post_id);
            $interaction_counts[$interaction] = wordpress_demo_theme_get_post_interaction_counts($visible_post_ids, $interaction);
        }

        $count = isset($interaction_counts[$interaction][$post_id]) ? (int) $interaction_counts[$interaction][$post_id] : 0;

        if($count === 0) {
            $empty_label = $interaction === 'like' ? __('No likes', 'wordpress-demo-theme') : __('No saves', 'wordpress-demo-theme');
            echo '<span aria-hidden="true">&mdash;</span><span class="screen-reader-text">' . esc_html($empty_label) . '</span>';
            return;
        }

        $summary = $interaction === 'like'
            ? sprintf(_n('%s like', '%s likes', $count, 'wordpress-demo-theme'), number_format_i18n($count))
            : sprintf(_n('%s save', '%s saves', $count, 'wordpress-demo-theme'), number_format_i18n($count));
        ?>
        <details class="wordpress-demo-theme-admin-interactions" data-post-admin-interactions-details>
            <summary><strong><?php echo esc_html($summary); ?></strong></summary>
            <div class="wordpress-demo-theme-admin-interactions-loader" data-post-admin-interactions-loader data-post-id="<?php echo esc_attr($post_id); ?>" data-interaction="<?php echo esc_attr($interaction); ?>" data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('wordpress_demo_theme_admin_post_interactions')); ?>" data-loading-text="<?php esc_attr_e('Loading users...', 'wordpress-demo-theme'); ?>" data-error-text="<?php esc_attr_e('Could not load users.', 'wordpress-demo-theme'); ?>">
                <p data-post-admin-interactions-status><?php esc_html_e('Expand to load user details.', 'wordpress-demo-theme'); ?></p>
                <ul data-post-admin-interactions-list></ul>
                <button type="button" class="button button-small" data-post-admin-interactions-more hidden><?php esc_html_e('Load more', 'wordpress-demo-theme'); ?></button>
            </div>
        </details>
        <?php
    }
}

if(!function_exists('wordpress_demo_theme_admin_post_interactions')) {
    function wordpress_demo_theme_admin_post_interactions() {
        check_ajax_referer('wordpress_demo_theme_admin_post_interactions', 'nonce');

        $post_id = isset($_POST['post_id']) && is_scalar($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        $interaction = isset($_POST['interaction']) && is_string($_POST['interaction']) ? sanitize_key(wp_unslash($_POST['interaction'])) : '';
        $page = isset($_POST['page']) && is_scalar($_POST['page']) ? max(1, absint($_POST['page'])) : 1;
        $post = get_post($post_id);

        if(!$post || $post->post_type !== 'post' || !in_array($interaction, array('like', 'save'), true) || !current_user_can('edit_post', $post_id)) {
            wp_send_json_error(array('message' => __('You cannot view this activity for the post.', 'wordpress-demo-theme')), 403);
        }

        $per_page = 20;
        $users = wordpress_demo_theme_get_post_interaction_users($post_id, $interaction, $per_page + 1, ($page - 1) * $per_page);
        $has_more = count($users) > $per_page;

        if($has_more) {
            array_pop($users);
        }

        $items = array_map(function($user) {
            $user_id = (int) $user->user_id;
            $created_at = ctype_digit((string) $user->meta_value) ? (int) $user->meta_value : 0;
            $has_time = $created_at >= 946684800;
            $exact_time = $has_time ? wp_date(get_option('date_format') . ' ' . get_option('time_format'), $created_at) : '';
            $relative_time = $has_time ? sprintf(__('%s ago', 'wordpress-demo-theme'), human_time_diff($created_at, time())) : __('Time unavailable', 'wordpress-demo-theme');

            return array(
                'name' => wordpress_demo_theme_get_user_name_from_record($user),
                'avatar_url' => get_avatar_url($user_id, array('size' => 56)),
                'edit_url' => current_user_can('edit_user', $user_id) ? get_edit_user_link($user_id) : '',
                'exact_time' => $exact_time,
                'time_label' => $exact_time !== '' ? $exact_time . ' (' . $relative_time . ')' : $relative_time,
            );
        }, $users);

        wp_send_json_success(array(
            'items' => $items,
            'has_more' => $has_more,
            'next_page' => $page + 1,
        ));
    }
}

add_filter('manage_post_posts_columns', 'wordpress_demo_theme_post_columns');
add_action('manage_post_posts_custom_column', 'wordpress_demo_theme_post_interaction_column', 10, 2);
add_action('wp_ajax_wordpress_demo_theme_admin_post_interactions', 'wordpress_demo_theme_admin_post_interactions');


if(!function_exists('wordpress_demo_theme_get_comment_reactions')) {
    function wordpress_demo_theme_get_comment_reactions($comment_id) {
        global $wpdb;

        $comment_id = absint($comment_id);
        $reactions = array('like' => 0, 'dislike' => 0, 'selected' => 'none');
        $comment = get_comment($comment_id);

        if(!$comment || (string) $comment->comment_approved !== '1') {
            return $reactions;
        }

        $meta_key = '_wordpress_demo_theme_comment_reaction_';
        $meta_pattern = $wpdb->esc_like($meta_key) . '%';
        $counts = $wpdb->get_results($wpdb->prepare(
            "SELECT meta_value, COUNT(DISTINCT meta_key) AS total FROM {$wpdb->commentmeta} WHERE comment_id = %d AND meta_key LIKE %s AND meta_value IN ('like', 'dislike') GROUP BY meta_value",
            $comment_id,
            $meta_pattern
        ));

        foreach($counts as $row) {
            $reactions[$row->meta_value] = (int) $row->total;
        }

        if(is_user_logged_in()) {
            $user_key = $meta_key . get_current_user_id();
            $selected = get_comment_meta($comment_id, $user_key, true);
            $reactions['selected'] = in_array($selected, array('like', 'dislike'), true) ? $selected : 'none';
        }

        return $reactions;
    }
}

if(!function_exists('wordpress_demo_theme_handle_comment_reaction')) {
    function wordpress_demo_theme_handle_comment_reaction() {
        if(!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('Please log in to react.', 'wordpress-demo-theme')), 401);
        }

        if(!check_ajax_referer('wordpress_demo_theme_comment_reaction', 'nonce', false)) {
            wp_send_json_error(array('message' => __('Invalid security token.', 'wordpress-demo-theme')), 403);
        }

        $comment_id = isset($_POST['comment_id']) && is_scalar($_POST['comment_id']) ? absint($_POST['comment_id']) : 0;
        $reaction = isset($_POST['reaction']) && is_string($_POST['reaction']) ? sanitize_key(wp_unslash($_POST['reaction'])) : '';
        $comment = get_comment($comment_id);

        if(!$comment || (string) $comment->comment_approved !== '1' || get_post_status($comment->comment_post_ID) !== 'publish' || post_password_required($comment->comment_post_ID)) {
            wp_send_json_error(array('message' => __('Invalid comment.', 'wordpress-demo-theme')), 400);
        }

        if(!in_array($reaction, array('none', 'like', 'dislike'), true)) {
            wp_send_json_error(array('message' => __('Invalid reaction.', 'wordpress-demo-theme')), 400);
        }

        $user_key = '_wordpress_demo_theme_comment_reaction_' . get_current_user_id();
        $time_key = '_wordpress_demo_theme_comment_reaction_at_' . get_current_user_id();
        $previous = get_comment_meta($comment_id, $user_key, true);

        if($reaction === 'none') {
            $updated = $previous ? delete_comment_meta($comment_id, $user_key) : true;
            delete_comment_meta($comment_id, $time_key);
        }
        else {
            $updated = update_comment_meta($comment_id, $user_key, $reaction);

            if($updated) {
                update_comment_meta($comment_id, $time_key, (string) time());
            }
        }

        if(!$updated && $previous !== $reaction) {
            wp_send_json_error(array('message' => __('Could not save your reaction.', 'wordpress-demo-theme')), 500);
        }

        $reactions = wordpress_demo_theme_get_comment_reactions($comment_id);
        wp_send_json_success($reactions);
    }
}

add_action('wp_ajax_wordpress_demo_theme_comment_reaction', 'wordpress_demo_theme_handle_comment_reaction');
add_action('wp_ajax_nopriv_wordpress_demo_theme_comment_reaction', 'wordpress_demo_theme_handle_comment_reaction');

if(!function_exists('wordpress_demo_theme_comment_columns')) {
    function wordpress_demo_theme_comment_columns($columns) {
        $updated_columns = array();
        $likes_added = false;

        foreach($columns as $column_key => $column_label) {
            if($column_key === 'response') {
                $updated_columns['wordpress_demo_theme_comment_reactions'] = __('Reactions', 'wordpress-demo-theme');
                $likes_added = true;
            }

            $updated_columns[$column_key] = $column_label;
        }

        if(!$likes_added) {
            $updated_columns['wordpress_demo_theme_comment_reactions'] = __('Reactions', 'wordpress-demo-theme');
        }

        return $updated_columns;
    }
}

if(!function_exists('wordpress_demo_theme_comment_reactions_column')) {
    function wordpress_demo_theme_comment_reactions_column($column, $comment_id) {
        static $reaction_counts = null;

        if($column !== 'wordpress_demo_theme_comment_reactions') {
            return;
        }

        if($reaction_counts === null) {
            global $wp_list_table;

            $visible_comment_ids = !empty($wp_list_table->items) ? wp_list_pluck($wp_list_table->items, 'comment_ID') : array($comment_id);
            $reaction_counts = wordpress_demo_theme_get_comment_reaction_counts($visible_comment_ids);
        }

        $counts = isset($reaction_counts[$comment_id]) ? $reaction_counts[$comment_id] : array('like' => 0, 'dislike' => 0);

        if(!$counts['like'] && !$counts['dislike']) {
            echo '<span aria-hidden="true">&mdash;</span><span class="screen-reader-text">' . esc_html__('No reactions', 'wordpress-demo-theme') . '</span>';
            return;
        }
        ?>
        <div class="wordpress-demo-theme-admin-reaction-groups">
            <?php foreach(array('like' => __('Likes', 'wordpress-demo-theme'), 'dislike' => __('Dislikes', 'wordpress-demo-theme')) as $reaction => $label) : ?>
                <?php if($counts[$reaction]) : ?>
                    <details class="wordpress-demo-theme-admin-interactions" data-comment-admin-reactions-details>
                        <summary><strong><?php echo esc_html($label . ': ' . number_format_i18n($counts[$reaction])); ?></strong></summary>
                        <div class="wordpress-demo-theme-admin-interactions-loader" data-comment-admin-reactions-loader data-comment-id="<?php echo esc_attr($comment_id); ?>" data-interaction="<?php echo esc_attr($reaction); ?>" data-ajax-action="wordpress_demo_theme_admin_comment_reactions" data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('wordpress_demo_theme_admin_comment_reactions')); ?>" data-loading-text="<?php esc_attr_e('Loading users...', 'wordpress-demo-theme'); ?>" data-error-text="<?php esc_attr_e('Could not load users.', 'wordpress-demo-theme'); ?>">
                            <p data-post-admin-interactions-status><?php esc_html_e('Expand to load user details.', 'wordpress-demo-theme'); ?></p>
                            <ul data-post-admin-interactions-list></ul>
                            <button type="button" class="button button-small" data-post-admin-interactions-more hidden><?php esc_html_e('Load more', 'wordpress-demo-theme'); ?></button>
                        </div>
                    </details>
                <?php else : ?>
                    <span><?php echo esc_html($label . ': 0'); ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php
    }
}

if(!function_exists('wordpress_demo_theme_admin_comment_reactions')) {
    function wordpress_demo_theme_admin_comment_reactions() {
        check_ajax_referer('wordpress_demo_theme_admin_comment_reactions', 'nonce');

        $comment_id = isset($_POST['comment_id']) && is_scalar($_POST['comment_id']) ? absint($_POST['comment_id']) : 0;
        $reaction = isset($_POST['interaction']) && is_string($_POST['interaction']) ? sanitize_key(wp_unslash($_POST['interaction'])) : '';
        $page = isset($_POST['page']) && is_scalar($_POST['page']) ? max(1, absint($_POST['page'])) : 1;
        $comment = get_comment($comment_id);

        if(!$comment || !in_array($reaction, array('like', 'dislike'), true) || !current_user_can('edit_comment', $comment_id)) {
            wp_send_json_error(array('message' => __('You cannot view reactions for this comment.', 'wordpress-demo-theme')), 403);
        }

        $per_page = 20;
        $users = wordpress_demo_theme_get_comment_reaction_users($comment_id, $reaction, $per_page + 1, ($page - 1) * $per_page);
        $has_more = count($users) > $per_page;

        if($has_more) {
            array_pop($users);
        }

        $items = array_map(function($user) {
            $user_id = (int) $user->user_id;
            $reacted_at = ctype_digit((string) $user->reacted_at) ? (int) $user->reacted_at : 0;
            $has_time = $reacted_at >= 946684800;
            $exact_time = $has_time ? wp_date(get_option('date_format') . ' ' . get_option('time_format'), $reacted_at) : '';
            $relative_time = $has_time ? sprintf(__('%s ago', 'wordpress-demo-theme'), human_time_diff($reacted_at, time())) : __('Time unavailable', 'wordpress-demo-theme');

            return array(
                'name' => wordpress_demo_theme_get_user_name_from_record($user),
                'avatar_url' => get_avatar_url($user_id, array('size' => 56)),
                'edit_url' => current_user_can('edit_user', $user_id) ? get_edit_user_link($user_id) : '',
                'exact_time' => $exact_time,
                'time_label' => $exact_time !== '' ? $exact_time . ' (' . $relative_time . ')' : $relative_time,
            );
        }, $users);

        wp_send_json_success(array(
            'items' => $items,
            'has_more' => $has_more,
            'next_page' => $page + 1,
        ));
    }
}

add_filter('manage_edit-comments_columns', 'wordpress_demo_theme_comment_columns');
add_action('manage_comments_custom_column', 'wordpress_demo_theme_comment_reactions_column', 10, 2);
add_action('wp_ajax_wordpress_demo_theme_admin_comment_reactions', 'wordpress_demo_theme_admin_comment_reactions');
