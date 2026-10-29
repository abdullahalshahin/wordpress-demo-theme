<?php
/**
 * Component: Breadcrumb
 *
 * Renders breadcrumb navigation using the
 * selected design and supplied items.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

$breadcrumb_items = isset($breadcrumb_items) && is_array($breadcrumb_items) ? array_values(array_filter($breadcrumb_items, function($item) {
    return is_array($item) && isset($item['title']) && is_scalar($item['title']);
})) : array();

if(empty($breadcrumb_items)) {
    return;
}

$available_designs = wordpress_demo_theme_get_available_designs('breadcrumb');
$selected_breadcrumb_design = get_theme_mod('wordpress_demo_theme_breadcrumb_design', 'design-1');
$last_index = count($breadcrumb_items) - 1;

switch($selected_breadcrumb_design) {
    case 'design-2':
        ?>
        <nav class="mb-6 flex min-w-0 items-center gap-2 overflow-hidden text-xs text-[var(--color-text-muted)] sm:text-sm" aria-label="<?php esc_attr_e('Breadcrumb', 'wordpress-demo-theme'); ?>">
            <ol class="flex flex-wrap items-center gap-x-2 gap-y-2 text-sm">
                <?php foreach($breadcrumb_items as $index => $item) : ?>
                    <li class="inline-flex items-center gap-2">
                        <?php if($index > 0) : ?>
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 shrink-0 text-[var(--color-text-muted)]" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m7.5 4.5 5 5.5-5 5.5"></path>
                            </svg>
                        <?php endif; ?>

                        <?php if($index !== $last_index && !empty($item['url'])) : ?>
                            <a href="<?php echo esc_url($item['url']); ?>" class="text-[var(--color-text-secondary)] transition-colors hover:text-[var(--color-primary)] focus-visible:text-[var(--color-primary)]">
                                <?php echo esc_html($item['title']); ?>
                            </a>
                        <?php else : ?>
                            <span class="<?php echo $index === $last_index ? 'font-semibold text-[var(--color-text-primary)]' : 'text-[var(--color-text-secondary)]'; ?>" <?php if($index === $last_index) : ?>aria-current="page"<?php endif; ?>>
                                <?php echo esc_html($item['title']); ?>
                            </span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
        <?php
        break;

    case 'design-3':
        ?>
        <nav class="mb-6 flex min-w-0 items-center gap-2 overflow-hidden text-xs text-[var(--color-text-muted)] sm:text-sm" aria-label="<?php esc_attr_e('Breadcrumb', 'wordpress-demo-theme'); ?>">
            <ol class="flex flex-wrap items-center gap-x-2 gap-y-2 text-sm">
                <?php foreach($breadcrumb_items as $index => $item) : ?>
                    <li class="inline-flex items-center gap-2">
                        <?php if($index > 0) : ?>
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 shrink-0 text-[var(--color-text-muted)]" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m7.5 4.5 5 5.5-5 5.5"></path>
                            </svg>
                        <?php endif; ?>

                        <?php if($index !== $last_index && !empty($item['url'])) : ?>
                            <a href="<?php echo esc_url($item['url']); ?>" class="text-[var(--color-text-secondary)] transition-colors hover:text-[var(--color-primary)] focus-visible:text-[var(--color-primary)]">
                                <?php echo esc_html($item['title']); ?>
                            </a>
                        <?php else : ?>
                            <span class="<?php echo $index === $last_index ? 'font-semibold text-[var(--color-text-primary)]' : 'text-[var(--color-text-secondary)]'; ?>" <?php if($index === $last_index) : ?>aria-current="page"<?php endif; ?>>
                                <?php echo esc_html($item['title']); ?>
                            </span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
        <?php
        break;

    case 'design-1':
    default:
        ?>
        <nav class="mb-6 flex min-w-0 items-center gap-2 overflow-hidden text-xs text-[var(--color-text-muted)] sm:text-sm" aria-label="<?php esc_attr_e('Breadcrumb', 'wordpress-demo-theme'); ?>">
            <?php foreach ($breadcrumb_items as $index => $item) : ?>
                <?php if ($index > 0) : ?>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" class="size-3.5 shrink-0" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m7.5 4.5 5 5.5-5 5.5"></path>
                    </svg>
                <?php endif; ?>
                <?php if ($index !== $last_index && !empty($item['url'])) : ?>
                    <a href="<?php echo esc_url($item['url']); ?>"
                    class="shrink-0 font-medium text-[var(--color-primary)] transition hover:text-[var(--color-primary-hover)]">
                        <?php echo esc_html($item['title']); ?>
                    </a>
                <?php else : ?>
                    <span class="truncate text-[var(--color-text-secondary)]"
                        <?php if ($index === $last_index) : ?>
                            aria-current="page"
                        <?php endif; ?>>
                        <?php echo esc_html($item['title']); ?>
                    </span>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
        <?php
        break;
}
