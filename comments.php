<?php
/**
 * Template: Comments
 *
 * Displays comment threads, reactions, navigation,
 * and the comment submission form.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

if(post_password_required()) { return; }

$count = (int) get_comments_number();
$open = comments_open();
$threaded = (bool) get_option('thread_comments');
$max_depth = $threaded ? max(2, (int) get_option('thread_comments_depth', 5)) : 1;
$comment_sort = isset($_GET['comment_sort']) && is_string($_GET['comment_sort']) && sanitize_key(wp_unslash($_GET['comment_sort'])) === 'oldest' ? 'oldest' : 'newest';

if($open && $threaded) {
    wp_enqueue_script('comment-reply');
}

if(!function_exists('wordpress_demo_theme_comment_callback')) {
    function wordpress_demo_theme_comment_callback($comment, $args, $depth) {
        $id = (int) $comment->comment_ID;
        $author = get_comment_author($comment);
        $author_url = get_comment_author_url($comment);
        $post_author = (int) get_post_field('post_author', $comment->comment_post_ID);
        $is_author = (int) $comment->user_id > 0 && (int) $comment->user_id === $post_author;
        $comment_url = get_comment_link($comment);
        $timestamp = strtotime($comment->comment_date_gmt . ' UTC');
        $relative_date = sprintf(__('%s ago', 'wordpress-demo-theme'), human_time_diff($timestamp ?: time(), time()));
        $reactions = wordpress_demo_theme_get_comment_reactions($id);
        ?>
        <li id="comment-<?php echo esc_attr($id); ?>" <?php comment_class('wordpress-demo-comment-thread relative min-w-0 list-none [&>.children]:ml-[18px] [&>.children]:mt-5 [&>.children]:list-none [&>.children]:p-0 [&>.children>li]:relative [&>.children>li]:pl-7 [&>.children>li+li]:mt-5 [&>.children>li]:before:pointer-events-none [&>.children>li]:before:absolute [&>.children>li]:before:bottom-[-20px] [&>.children>li]:before:left-0 [&>.children>li]:before:top-0 [&>.children>li]:before:border-l [&>.children>li]:before:border-[var(--color-border)] [&>.children>li:last-child]:before:hidden [&>.children>li]:after:pointer-events-none [&>.children>li]:after:absolute [&>.children>li]:after:left-0 [&>.children>li]:after:top-0 [&>.children>li]:after:h-[18px] [&>.children>li]:after:w-7 [&>.children>li]:after:rounded-bl-xl [&>.children>li]:after:border-b [&>.children>li]:after:border-l [&>.children>li]:after:border-[var(--color-border)] max-sm:[&>.children_.children]:ml-0 max-sm:[&>.children_.children>li]:pl-2.5 max-sm:[&>.children_.children>li]:after:w-2.5 max-sm:[&>.children_.wordpress-demo-comment-thread:has(>.children)>article]:before:left-0 sm:[&>.children]:ml-5 sm:[&>.children>li]:pl-8 sm:[&>.children>li]:after:h-5 sm:[&>.children>li]:after:w-8', $comment); ?>>
            <article id="comment-body-<?php echo esc_attr($id); ?>" class="relative min-w-0 scroll-mt-24 before:pointer-events-none before:absolute before:bottom-[-20px] before:left-[18px] before:top-10 before:hidden before:border-l before:border-[var(--color-border)] has-[+.children]:before:block sm:before:left-5 sm:before:top-11">     
                <div class="absolute left-0 top-0 flex size-9 items-center justify-center overflow-hidden rounded-full bg-[var(--color-primary-subtle)] text-sm font-semibold text-[var(--color-primary-on-subtle)] sm:size-10">
                    <?php echo get_avatar($comment, 96, '', $author, array(
                        'class' => 'avatar block size-full rounded-full object-cover',
                        'loading' => 'lazy'
                    )); ?>
                </div>

                <div class="min-w-0 pl-12 sm:pl-14">
                    <div class="flex min-h-6 flex-wrap items-center gap-x-2.5 gap-y-0.5 sm:min-h-8">
                        <div class="flex min-w-0 items-center gap-1.5">
                            <?php if($author_url && $author_url !== 'http://') : ?>
                                <a href="<?php echo esc_url($author_url); ?>" rel="ugc external nofollow" class="break-words text-sm font-semibold leading-5 text-[var(--color-text-heading)] transition-colors hover:text-[var(--color-primary)] sm:text-base"><?php echo esc_html($author); ?></a>
                            <?php else : ?>
                                <span class="break-words text-sm font-semibold leading-5 text-[var(--color-text-heading)] sm:text-base"><?php echo esc_html($author); ?></span>
                            <?php endif; ?>
                            <?php if($is_author) : ?>
                                <span class="inline-flex shrink-0 text-[var(--color-primary)]" title="<?php esc_attr_e('Post author', 'wordpress-demo-theme'); ?>">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M18.9905 19H19M18.9905 19C18.3678 19.6175 17.2393 19.4637 16.4479 19.4637C15.4765 19.4637 15.0087 19.6537 14.3154 20.347C13.7251 20.9374 12.9337 22 12 22C11.0663 22 10.2749 20.9374 9.68457 20.347C8.99128 19.6537 8.52349 19.4637 7.55206 19.4637C6.76068 19.4637 5.63218 19.6175 5.00949 19C4.38181 18.3776 4.53628 17.2444 4.53628 16.4479C4.53628 15.4414 4.31616 14.9786 3.59938 14.2618C2.53314 13.1956 2.00002 12.6624 2 12C2.00001 11.3375 2.53312 10.8044 3.59935 9.73817C4.2392 9.09832 4.53628 8.46428 4.53628 7.55206C4.53628 6.76065 4.38249 5.63214 5 5.00944C5.62243 4.38178 6.7556 4.53626 7.55208 4.53626C8.46427 4.53626 9.09832 4.2392 9.73815 3.59937C10.8044 2.53312 11.3375 2 12 2C12.6625 2 13.1956 2.53312 14.2618 3.59937C14.9015 4.23907 15.5355 4.53626 16.4479 4.53626C17.2393 4.53626 18.3679 4.38247 18.9906 5C19.6182 5.62243 19.4637 6.75559 19.4637 7.55206C19.4637 8.55858 19.6839 9.02137 20.4006 9.73817C21.4669 10.8044 22 11.3375 22 12C22 12.6624 21.4669 13.1956 20.4006 14.2618C19.6838 14.9786 19.4637 15.4414 19.4637 16.4479C19.4637 17.2444 19.6182 18.3776 18.9905 19Z"></path>
                                        <path d="M9 12.8929L10.8 14.5L15 9.5" stroke="var(--color-primary-text)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>

                                    <span class="sr-only"><?php esc_html_e('Post author', 'wordpress-demo-theme'); ?></span>
                                </span>
                            <?php endif; ?>
                        </div>
                        <a href="<?php echo esc_url($comment_url); ?>" class="text-xs leading-5 text-[var(--color-text-muted)] transition-colors hover:text-[var(--color-primary)]" title="<?php echo esc_attr(get_comment_date('', $comment)); ?>">
                            <time datetime="<?php echo esc_attr(get_comment_date('c', $comment)); ?>"><?php echo esc_html($relative_date); ?></time>
                        </a>
                    </div>

                    <?php if((string) $comment->comment_approved === '0') : ?>
                        <p class="mt-1.5 text-xs text-[var(--color-primary)]">
                            <?php esc_html_e('Your comment is awaiting moderation.', 'wordpress-demo-theme'); ?>
                        </p>
                    <?php endif; ?>

                    <div class="min-w-0 break-words text-sm leading-6 text-[var(--color-text-primary)] [&_a]:text-[var(--color-primary)] [&_a]:underline [&_blockquote]:border-l-2 [&_blockquote]:border-[var(--color-border)] [&_blockquote]:pl-3 [&_img]:h-auto [&_img]:max-w-full [&_p]:mb-2 [&_p:last-child]:mb-0">
                        <?php comment_text($comment); ?>
                    </div>

                    <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-[var(--color-text-muted)]" data-comment-reactions="<?php echo esc_attr($id); ?>" data-reaction-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-reaction-nonce="<?php echo esc_attr(wp_create_nonce('wordpress_demo_theme_comment_reaction')); ?>" data-reaction-error="<?php esc_attr_e('Could not save your reaction. Please try again.', 'wordpress-demo-theme'); ?>">
                        <?php if((string) $comment->comment_approved === '1') : ?>
                            <div class="flex items-center gap-3">
                                <?php foreach(array('like' => __('Like', 'wordpress-demo-theme'), 'dislike' => __('Dislike', 'wordpress-demo-theme')) as $reaction => $reaction_label) : ?>
                                    <?php
                                    $reaction_class = 'inline-flex min-h-8 items-center gap-1 transition-colors hover:text-[var(--color-primary)] disabled:opacity-50 aria-pressed:text-[var(--color-primary)]';
                                    $reaction_icon_fill = $reactions['selected'] === $reaction ? 'currentColor' : 'none';
                                    $reaction_icon = '<svg class="size-4' . ($reaction === 'dislike' ? ' rotate-180' : '') . '" viewBox="0 0 24 24" fill="' . $reaction_icon_fill . '" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" aria-hidden="true"><path d="M2 10h4v11H2zM8 10l5-8h1a2 2 0 0 1 2 2l-1 5h5a2 2 0 0 1 2 2l-2 8a3 3 0 0 1-3 2H8z"></path></svg>';
                                    ?>
                                    <?php if(is_user_logged_in()) : ?>
                                        <button type="button" class="<?php echo esc_attr($reaction_class); ?>" data-comment-reaction="<?php echo esc_attr($reaction); ?>" aria-pressed="<?php echo $reactions['selected'] === $reaction ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr($reaction_label); ?>">
                                            <?php echo $reaction_icon; ?>
                                            <span data-reaction-count="<?php echo esc_attr($reaction); ?>"><?php echo esc_html(number_format_i18n($reactions[$reaction])); ?></span>
                                        </button>
                                    <?php else : ?>
                                        <a href="<?php echo esc_url(wordpress_demo_theme_login_url($comment_url)); ?>" class="<?php echo esc_attr($reaction_class); ?>" aria-label="<?php echo esc_attr(sprintf(__('Sign in to %s this comment', 'wordpress-demo-theme'), strtolower($reaction_label))); ?>">
                                            <?php echo $reaction_icon; ?>
                                            <span><?php echo esc_html(number_format_i18n($reactions[$reaction])); ?></span>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <?php
                            if(comments_open($comment->comment_post_ID) && $depth < (int) $args['max_depth'] && get_option('thread_comments')) {
                                comment_reply_link(array_merge($args, array(
                                    'add_below' => 'comment-reply-target',
                                    'depth' => $depth,
                                    'max_depth' => $args['max_depth'],
                                    'reply_text' => '<svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24" width="24" height="24" color="currentColor" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"> <path d="M7.5 12H13.5M7.5 8H10.5" stroke-linecap="round"></path> <path d="M8.5 20C9.55038 20.8697 10.8145 21.4238 12.2635 21.5188C13.4052 21.5937 14.5971 21.5936 15.7365 21.5188C16.1288 21.4931 16.5565 21.4007 16.9248 21.251C17.3345 21.0845 17.5395 21.0012 17.6437 21.0138C17.7478 21.0264 17.8989 21.1364 18.2011 21.3563C18.7339 21.744 19.4051 22.0225 20.4005 21.9986C20.9038 21.9865 21.1555 21.9804 21.2681 21.7909C21.3808 21.6013 21.2405 21.3389 20.9598 20.8141C20.5706 20.0862 20.324 19.2529 20.6977 18.5852C21.3413 17.6315 21.8879 16.5021 21.9678 15.2823C22.0107 14.6269 22.0107 13.9481 21.9678 13.2927C21.9146 12.4799 21.7173 11.7073 21.4012 11" stroke-linecap="round"></path> <path d="M12.345 17.4868C15.9006 17.2526 18.7328 14.4069 18.9658 10.8344C19.0114 10.1353 19.0114 9.41131 18.9658 8.71219C18.7328 5.13969 15.9006 2.29401 12.345 2.05985C11.132 1.97997 9.86553 1.98013 8.65499 2.05985C5.09943 2.29401 2.26725 5.13969 2.0342 8.71219C1.9886 9.41131 1.9886 10.1353 2.0342 10.8344C2.11908 12.1356 2.69992 13.3403 3.38372 14.3576C3.78076 15.0697 3.51873 15.9586 3.10518 16.735C2.807 17.2948 2.65791 17.5747 2.77762 17.7769C2.89732 17.9791 3.16472 17.9856 3.69951 17.9985C4.75712 18.024 5.47028 17.7269 6.03638 17.3134C6.35744 17.0788 6.51798 16.9615 6.62862 16.9481C6.73926 16.9346 6.957 17.0234 7.39241 17.2011C7.78374 17.3608 8.23812 17.4593 8.65499 17.4868C9.86553 17.5665 11.132 17.5666 12.345 17.4868Z"></path> </svg><span>' . esc_html__('Reply', 'wordpress-demo-theme') . '</span>',
                                    'before' => '<span class="[&_a]:inline-flex [&_a]:min-h-8 [&_a]:items-center [&_a]:gap-1.5 [&_a:hover]:text-[var(--color-primary)]">',
                                    'after' => '</span>',
                                )), $comment);
                            }
                            ?>
    
                            <details class="relative" data-comment-actions>
                                <summary class="flex size-8 cursor-pointer list-none items-center justify-center rounded-full transition-colors hover:bg-[var(--color-action-surface)] hover:text-[var(--color-primary)] [&::-webkit-details-marker]:hidden" aria-label="<?php esc_attr_e('More comment options', 'wordpress-demo-theme'); ?>">
                                    <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="5" cy="12" r="2"></circle><circle cx="12" cy="12" r="2"></circle><circle cx="19" cy="12" r="2"></circle></svg>
                                </summary>
                                <div class="absolute right-0 top-full z-10 mt-1 w-40 rounded-lg border border-[var(--color-border)] bg-[var(--color-card)] p-1 shadow-[var(--shadow-md)]">
                                    <button type="button" class="block min-h-9 w-full rounded-md px-2.5 py-1.5 text-left text-xs hover:bg-[var(--color-action-surface)] hover:text-[var(--color-primary)]" data-copy-post-link data-post-url="<?php echo esc_url($comment_url); ?>" data-copied-text="<?php esc_attr_e('Copied!', 'wordpress-demo-theme'); ?>" data-copy-error-text="<?php esc_attr_e('Copy failed. Try again.', 'wordpress-demo-theme'); ?>"><span data-copy-post-link-text role="status"><?php esc_html_e('Copy comment link', 'wordpress-demo-theme'); ?></span></button>
                                    <?php edit_comment_link(esc_html__('Edit comment', 'wordpress-demo-theme'), '<span class="[&_a]:block [&_a]:rounded-md [&_a]:px-2.5 [&_a]:py-1.5 [&_a]:text-xs [&_a:hover]:bg-[var(--color-action-surface)]">', '</span>'); ?>
                                </div>
                            </details>
                            <span class="hidden w-full text-xs text-[var(--color-error)]" data-reaction-status role="status"></span>
                        <?php endif; ?>
                    </div>

                    <div id="comment-reply-target-<?php echo esc_attr($id); ?>" class="mt-2"></div>
                </div>
            </article>
        <?php
    }
}

$commenter  = wp_get_current_commenter();
$required   = (bool) get_option('require_name_email');
$required_attr = $required ? ' required="required" aria-required="true"' : '';
$required_mark = $required ? ' <span class="text-[var(--color-error)]" aria-hidden="true">*</span>' : '';
$input_class = 'block w-full rounded-lg border border-[var(--color-border)] bg-[var(--color-input)] px-3 py-2.5 text-sm leading-5 text-[var(--color-text-primary)] outline-none transition-colors placeholder:text-[var(--color-text-muted)] focus:border-[var(--color-primary)] focus:ring-2 focus:ring-[var(--color-focus-ring)]';
$label_class = 'mb-1.5 block text-xs font-medium text-[var(--color-text-primary)]';

$fields = array(
    'author' => '<p class="comment-form-author min-w-0"><label class="' . esc_attr($label_class) . '" for="author">' . esc_html__('Name', 'wordpress-demo-theme') . $required_mark . '</label><input class="' . esc_attr($input_class) . '" id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author'] ?? '') . '" autocomplete="name" maxlength="245"' . $required_attr . ' placeholder="' . esc_attr__('Your name', 'wordpress-demo-theme') . '"></p>',
    'email'  => '<p class="comment-form-email min-w-0"><label class="' . esc_attr($label_class) . '" for="email">' . esc_html__('Email', 'wordpress-demo-theme') . $required_mark . '</label><input class="' . esc_attr($input_class) . '" id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email'] ?? '') . '" autocomplete="email" maxlength="100"' . $required_attr . ' placeholder="' . esc_attr__('you@example.com', 'wordpress-demo-theme') . '"></p>',
);

if ((bool) get_option('show_comments_cookies_opt_in')) {
    $fields['cookies'] = '<p class="comment-form-cookies-consent col-span-full flex items-start gap-2.5"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" class="mt-0.5 size-4 shrink-0 cursor-pointer accent-[var(--color-primary)]"' . (!empty($commenter['comment_author_email']) ? ' checked="checked"' : '') . '><label class="cursor-pointer text-xs leading-5 text-[var(--color-text-secondary)]" for="wp-comment-cookies-consent">' . esc_html__('Save my name and email in this browser for the next time I comment.', 'wordpress-demo-theme') . '</label></p>';
}
?>
<section id="comments" class="min-w-0 scroll-mt-24 text-[var(--color-text-primary)]" aria-label="<?php esc_attr_e('Comments', 'wordpress-demo-theme'); ?>">
    <header class="mb-6 flex flex-wrap items-center justify-between gap-3 sm:mb-8">
        <h2 class="flex items-center gap-2.5 text-xl font-semibold tracking-tight text-[var(--color-text-heading)]">
            <?php esc_html_e('Comments', 'wordpress-demo-theme'); ?>
            <span class="inline-flex min-h-6 min-w-7 items-center justify-center rounded-full bg-[var(--color-primary)] px-2 text-xs font-semibold text-[var(--color-primary-text)]"><?php echo esc_html(number_format_i18n($count)); ?></span>
        </h2>
        <?php if($count > 1) : ?>
            <details class="relative text-xs text-[var(--color-text-secondary)]" data-comment-actions>
                <summary class="flex min-h-8 cursor-pointer list-none items-center gap-1.5 rounded-lg px-1 hover:text-[var(--color-primary)] [&::-webkit-details-marker]:hidden">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 20V4m-4 4 4-4 4 4m4-4v16m-4-4 4 4 4-4"></path></svg>
                    <?php echo $comment_sort === 'oldest' ? esc_html__('Oldest first', 'wordpress-demo-theme') : esc_html__('Most recent', 'wordpress-demo-theme'); ?>
                    <svg class="size-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 7.5 5 5 5-5"></path></svg>
                </summary>
                <div class="absolute right-0 top-full z-20 mt-1 w-36 rounded-lg border border-[var(--color-border)] bg-[var(--color-card)] p-1 shadow-[var(--shadow-md)]">
                    <?php foreach(array('newest' => __('Most recent', 'wordpress-demo-theme'), 'oldest' => __('Oldest first', 'wordpress-demo-theme')) as $sort => $sort_label) : ?>
                        <a href="<?php echo esc_url(add_query_arg('comment_sort', $sort, get_permalink()) . '#comments'); ?>" class="block rounded-md px-2.5 py-1.5 hover:bg-[var(--color-action-surface)] hover:text-[var(--color-primary)]"<?php echo $comment_sort === $sort ? ' aria-current="true"' : ''; ?>><?php echo esc_html($sort_label); ?></a>
                    <?php endforeach; ?>
                </div>
            </details>
        <?php endif; ?>
    </header>

    <?php if (have_comments()) : ?>
        <ol class="wordpress-demo-comment-list m-0 list-none space-y-7 p-0 sm:space-y-8">
            <?php wp_list_comments(array(
                'style'       => 'ol',
                'type'        => 'comment',
                'format'      => 'html5',
                'short_ping'  => true,
                'avatar_size' => 40,
                'max_depth'   => $max_depth,
                'reverse_top_level' => $comment_sort === 'newest',
                'callback'    => 'wordpress_demo_theme_comment_callback',
            )); ?>
        </ol>
        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav class="mt-6 flex flex-wrap justify-between gap-3 border-t border-[var(--color-border)] pt-4 text-xs font-semibold text-[var(--color-primary)]" aria-label="<?php esc_attr_e('Comment navigation', 'wordpress-demo-theme'); ?>">
                <span><?php previous_comments_link(esc_html__('← Older Comments', 'wordpress-demo-theme')); ?></span>
                <span><?php next_comments_link(esc_html__('Newer Comments →', 'wordpress-demo-theme')); ?></span>
            </nav>
        <?php endif; ?>
    <?php elseif ($open) : ?>
        <div class="rounded-xl border border-dashed border-[var(--color-border-strong)] bg-[var(--color-surface-soft)] px-4 py-7 text-center">
            <p class="mt-1.5 text-xs leading-5 text-[var(--color-text-muted)]"><?php esc_html_e('Be the first to share a thought or ask a question.', 'wordpress-demo-theme'); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!$open && $count) : ?>
        <p class="mt-6 rounded-lg border border-[var(--color-border)] bg-[var(--color-surface-soft)] p-3 text-center text-xs text-[var(--color-text-muted)]"><?php esc_html_e('Comments are closed.', 'wordpress-demo-theme'); ?></p>
    <?php endif; ?>

    <?php if ($open) : ?>
        <div class="mt-6">
            <?php
            comment_form(array(
                'title_reply'        => __('Share your thoughts', 'wordpress-demo-theme'),
                'title_reply_to'     => __('Reply to %s', 'wordpress-demo-theme'),
                'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
                'title_reply_after'  => '</h3>',
                'cancel_reply_link'  => __('Cancel Reply', 'wordpress-demo-theme'),
                'label_submit'       => __('Post Comment', 'wordpress-demo-theme'),
                'class_container'    => 'comment-respond rounded-xl border border-[var(--color-border)] bg-[var(--color-card)] p-4 sm:p-5 [&_.comment-reply-title]:mb-4 [&_.comment-reply-title]:text-lg [&_.comment-reply-title]:font-semibold [&_.comment-reply-title]:leading-6 [&_.comment-reply-title]:text-[var(--color-text-heading)] [&_#cancel-comment-reply-link]:ml-2.5 [&_#cancel-comment-reply-link]:text-xs [&_#cancel-comment-reply-link]:font-normal [&_#cancel-comment-reply-link]:text-[var(--color-primary)]',
                'class_form'         => 'comment-form grid grid-cols-1 gap-3 sm:grid-cols-2',
                'class_submit'       => 'inline-flex min-h-10 w-full cursor-pointer items-center justify-center rounded-full border-0 bg-[var(--color-primary)] px-5 py-2.5 text-xs font-semibold text-[var(--color-primary-text)] transition-colors hover:bg-[var(--color-primary-hover)] sm:w-auto',
                'fields'             => $fields,
                'comment_field'      => '<p class="comment-form-comment col-span-full"><label class="' . esc_attr($label_class) . '" for="comment">' . esc_html__('Your Comment', 'wordpress-demo-theme') . ' <span class="text-[var(--color-error)]" aria-hidden="true">*</span></label><textarea class="' . esc_attr($input_class) . ' min-h-28 resize-y leading-6" id="comment" name="comment" rows="5" required="required" aria-required="true" placeholder="' . esc_attr__('What would you like to add?', 'wordpress-demo-theme') . '"></textarea></p>',
                'comment_notes_before' => '<p class="comment-notes col-span-full text-xs leading-5 text-[var(--color-text-muted)]">' . esc_html__('Your email address will not be published. Required fields are marked *.', 'wordpress-demo-theme') . '</p>',
                'logged_in_as'       => '<p class="logged-in-as col-span-full text-xs leading-5 text-[var(--color-text-secondary)]">' . sprintf(
                    wp_kses_post(__('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s">Log out?</a>', 'wordpress-demo-theme')),
                    esc_url(get_edit_user_link()),
                    esc_html(wp_get_current_user()->display_name),
                    esc_url(wp_logout_url(get_permalink()))
                ) . '</p>',
                'submit_field'       => '<p class="form-submit col-span-full">%1$s %2$s</p>',
                'format'             => 'html5',
            ));
            ?>
        </div>
    <?php endif; ?>
</section>
