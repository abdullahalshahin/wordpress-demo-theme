<?php
/**
 * Admin: Sanitization
 *
 * Provides sanitization callbacks for theme settings.
 *
 * @package WordPress_Demo_Theme
 */

if(!defined('ABSPATH')) {
    exit;
}

if(!function_exists('wordpress_demo_theme_sanitize_footer_sections')) {
    function wordpress_demo_theme_sanitize_footer_sections($sections) {
        if(!is_array($sections)) {
            return array();
        }

        $allowed_block_types = array('image', 'heading', 'description', 'social_links', 'navigation_menu', 'contact_details', 'custom_html');
        $sanitized_sections = array();

        foreach($sections as $section) {
            if(!is_array($section)) {
                continue;
            }

            $sanitized_section = array(
                'name' => isset($section['name']) ? sanitize_text_field($section['name']) : '',
                'blocks' => array()
            );

            $blocks = isset($section['blocks']) && is_array($section['blocks']) ? $section['blocks'] : array();

            foreach($blocks as $block) {
                if(!is_array($block)) {
                    continue;
                }

                $type = isset($block['type']) ? sanitize_key($block['type']) : '';

                if(!in_array($type, $allowed_block_types, true)) {
                    continue;
                }

                $sanitized_block = array('type' => $type);

                if($type === 'image') {
                    $sanitized_block['image_id'] = isset($block['image_id']) ? absint($block['image_id']) : 0;
                    $sanitized_block['use_site_logo'] = !empty($block['use_site_logo']);
                }
                elseif($type === 'heading' || $type === 'description') {
                    $sanitized_block['text'] = isset($block['text']) ? sanitize_textarea_field($block['text']) : '';
                }
                elseif($type === 'social_links') {
                    $sanitized_block['items'] = wordpress_demo_theme_sanitize_footer_link_items($block['items'] ?? array());
                }
                elseif($type === 'navigation_menu') {
                    $sanitized_block['menu_id'] = isset($block['menu_id']) ? absint($block['menu_id']) : 0;
                }
                elseif($type === 'contact_details') {
                    $sanitized_block['items'] = wordpress_demo_theme_sanitize_footer_contact_items($block['items'] ?? array());
                }
                elseif($type === 'custom_html') {
                    $html = isset($block['html']) && is_string($block['html']) ? $block['html'] : '';
                    $sanitized_block['is_unfiltered_html'] = current_user_can('unfiltered_html');
                    $sanitized_block['html'] = $sanitized_block['is_unfiltered_html'] ? $html : wp_kses_post($html);
                }

                $sanitized_section['blocks'][] = $sanitized_block;
            }

            $sanitized_sections[] = $sanitized_section;
        }

        return $sanitized_sections;
    }
}

if(!function_exists('wordpress_demo_theme_sanitize_footer_link_items')) {
    function wordpress_demo_theme_sanitize_footer_link_items($items) {
        if(!is_array($items)) {
            return array();
        }

        $sanitized_items = array();

        foreach($items as $item) {
            if(!is_array($item)) {
                continue;
            }

            $sanitized_items[] = array(
                'image_id' => isset($item['image_id']) ? absint($item['image_id']) : 0,
                'url' => isset($item['url']) ? esc_url_raw($item['url']) : ''
            );
        }

        return $sanitized_items;
    }
}

if(!function_exists('wordpress_demo_theme_sanitize_footer_contact_items')) {
    function wordpress_demo_theme_sanitize_footer_contact_items($items) {
        if(!is_array($items)) {
            return array();
        }

        $sanitized_items = array();

        foreach($items as $item) {
            if(!is_array($item)) {
                continue;
            }

            $sanitized_items[] = array(
                'image_id' => isset($item['image_id']) ? absint($item['image_id']) : 0,
                'label' => isset($item['label']) ? sanitize_text_field($item['label']) : ''
            );
        }

        return $sanitized_items;
    }
}
