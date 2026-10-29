(function() {
    'use strict';

    var builder = document.querySelector('[data-footer-builder]');
    var data_element = document.querySelector('[data-footer-builder-data]');
    var form = document.querySelector('[data-footer-settings-form]');

    if(!builder || !data_element || !form) {
        return;
    }

    var config = JSON.parse(data_element.textContent || '{}');
    var labels = config.labels || {};
    var sections = Array.isArray(config.sections) ? config.sections : [];
    var block_types = ['image', 'heading', 'description', 'social_links', 'navigation_menu', 'contact_details', 'custom_html'];

    var create_element = function(tag_name, class_name, text) {
        var element = document.createElement(tag_name);

        if(class_name) {
            element.className = class_name;
        }

        if(text !== undefined) {
            element.textContent = text;
        }

        return element;
    };

    var create_button = function(text, class_name, callback) {
        var button = create_element('button', class_name || 'button', text);
        button.type = 'button';
        button.addEventListener('click', callback);
        return button;
    };

    var create_icon_button = function(label, icon, callback) {
        var button = create_button('', 'button wordpress-demo-theme-icon-button', callback);
        var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        var paths = {
            up: 'M12 19V5m0 0-6 6m6-6 6 6',
            down: 'M12 5v14m0 0 6-6m-6 6-6-6',
            remove: 'M4 7h16m-10 4v6m4-6v6M9 7l1-3h4l1 3m3 0-1 14H7L6 7'
        };

        button.setAttribute('aria-label', label);
        button.title = label;
        svg.setAttribute('viewBox', '0 0 24 24');
        svg.setAttribute('fill', 'none');
        svg.setAttribute('stroke', 'currentColor');
        svg.setAttribute('stroke-width', '1.8');
        svg.setAttribute('aria-hidden', 'true');
        path.setAttribute('d', paths[icon]);
        path.setAttribute('stroke-linecap', 'round');
        path.setAttribute('stroke-linejoin', 'round');
        svg.appendChild(path);
        button.appendChild(svg);
        return button;
    };

    var create_field = function(label_text, input) {
        var label = create_element('label', 'wordpress-demo-theme-field');
        label.appendChild(create_element('span', '', label_text));
        label.appendChild(input);
        return label;
    };

    var create_input = function(value, placeholder, callback, type) {
        var input = create_element('input');
        input.type = type || 'text';
        input.value = value || '';
        input.placeholder = placeholder || '';
        input.className = 'regular-text';
        input.addEventListener('input', function() {
            callback(input.value);
        });
        return input;
    };

    var move_item = function(items, index, direction) {
        var new_index = index + direction;

        if(new_index < 0 || new_index >= items.length) {
            return;
        }

        var item = items[index];
        items[index] = items[new_index];
        items[new_index] = item;
        render_builder();
    };

    var open_media_library = function(callback) {
        if(!window.wp || !wp.media) {
            return;
        }

        var media_frame = wp.media({
            title: labels.media_title,
            button: { text: labels.media_button },
            library: { type: 'image' },
            multiple: false
        });

        media_frame.on('select', function() {
            var attachment = media_frame.state().get('selection').first().toJSON();
            var preview_url = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
            callback(parseInt(attachment.id, 10) || 0, preview_url || '');
        });

        media_frame.open();
    };

    var render_image_control = function(item, use_site_logo) {
        var wrapper = create_element('div', 'wordpress-demo-theme-image-field');
        var use_existing_logo = use_site_logo && item.use_site_logo;
        var image_url = use_existing_logo ? config.site_logo.url : item.image_url;

        if(image_url) {
            var preview = create_element('div', 'wordpress-demo-theme-image-preview');
            var image = create_element('img');
            image.src = image_url;
            image.alt = '';
            preview.appendChild(image);
            wrapper.appendChild(preview);
        }

        if(!use_existing_logo) {
            wrapper.appendChild(create_button(image_url ? labels.replace_image : labels.select_image, 'button button-secondary', function() {
                open_media_library(function(image_id, selected_url) {
                    item.image_id = image_id;
                    item.image_url = selected_url;
                    render_builder();
                });
            }));

            if(item.image_id) {
                wrapper.appendChild(create_button(labels.remove_image, 'button button-link-delete', function() {
                    item.image_id = 0;
                    item.image_url = '';
                    render_builder();
                }));
            }
        }

        return wrapper;
    };

    var render_item_actions = function(items, index, remove_label) {
        var actions = create_element('div', 'wordpress-demo-theme-inline-actions');
        var up_button = create_icon_button(labels.move_up, 'up', function() {
            move_item(items, index, -1);
        });
        var down_button = create_icon_button(labels.move_down, 'down', function() {
            move_item(items, index, 1);
        });
        up_button.disabled = index === 0;
        down_button.disabled = index === items.length - 1;
        actions.appendChild(up_button);
        actions.appendChild(down_button);
        var remove_button = create_icon_button(remove_label, 'remove', function() {
            if(window.confirm(labels.confirm_remove_item)) {
                items.splice(index, 1);
                render_builder();
            }
        });
        remove_button.classList.add('button-link-delete');
        actions.appendChild(remove_button);
        return actions;
    };

    var render_social_links = function(block, container) {
        block.items = Array.isArray(block.items) ? block.items : [];

        block.items.forEach(function(item, index) {
            var card = create_element('div', 'wordpress-demo-theme-nested-card');
            card.appendChild(render_image_control(item, false));
            card.appendChild(create_field(labels.url, create_input(item.url, 'https://', function(value) {
                item.url = value;
            }, 'url')));
            card.appendChild(render_item_actions(block.items, index, labels.remove_social_link));
            container.appendChild(card);
        });

        container.appendChild(create_button(labels.add_social_link, 'button button-secondary', function() {
            block.items.push({ image_id: 0, image_url: '', url: '' });
            render_builder();
        }));
    };

    var render_contact_details = function(block, container) {
        block.items = Array.isArray(block.items) ? block.items : [];

        block.items.forEach(function(item, index) {
            var card = create_element('div', 'wordpress-demo-theme-nested-card');
            card.appendChild(render_image_control(item, false));
            card.appendChild(create_field(labels.label, create_input(item.label, labels.label, function(value) {
                item.label = value;
            })));
            card.appendChild(render_item_actions(block.items, index, labels.remove_contact));
            container.appendChild(card);
        });

        container.appendChild(create_button(labels.add_contact, 'button button-secondary', function() {
            block.items.push({ image_id: 0, image_url: '', label: '' });
            render_builder();
        }));
    };

    var render_block_fields = function(block, container) {
        if(block.type === 'image') {
            var logo_label = create_element('label', 'wordpress-demo-theme-checkbox-field');
            var logo_checkbox = create_element('input');
            logo_checkbox.type = 'checkbox';
            logo_checkbox.checked = !!block.use_site_logo;
            logo_checkbox.addEventListener('change', function() {
                block.use_site_logo = logo_checkbox.checked;
                render_builder();
            });
            logo_label.appendChild(logo_checkbox);
            logo_label.appendChild(document.createTextNode(labels.use_site_logo));
            container.appendChild(logo_label);
            container.appendChild(render_image_control(block, true));
        }
        else if(block.type === 'heading') {
            container.appendChild(create_field(labels.heading_text, create_input(block.text, labels.heading_text, function(value) {
                block.text = value;
            })));
        }
        else if(block.type === 'description') {
            var description = create_element('textarea', 'large-text');
            description.rows = 4;
            description.value = block.text || '';
            description.addEventListener('input', function() {
                block.text = description.value;
            });
            container.appendChild(create_field(labels.description_text, description));
        }
        else if(block.type === 'social_links') {
            render_social_links(block, container);
        }
        else if(block.type === 'navigation_menu') {
            var menu_select = create_element('select');
            var empty_option = create_element('option', '', labels.select_menu);
            empty_option.value = '0';
            menu_select.appendChild(empty_option);

            (config.menus || []).forEach(function(menu) {
                var option = create_element('option', '', menu.name);
                option.value = String(menu.id);
                option.selected = parseInt(block.menu_id, 10) === parseInt(menu.id, 10);
                menu_select.appendChild(option);
            });

            menu_select.addEventListener('change', function() {
                block.menu_id = parseInt(menu_select.value, 10) || 0;
            });
            container.appendChild(create_field(labels.navigation_menu, menu_select));
        }
        else if(block.type === 'contact_details') {
            render_contact_details(block, container);
        }
        else if(block.type === 'custom_html') {
            var html = create_element('textarea', 'large-text code');
            html.rows = 8;
            html.value = block.html || '';
            html.addEventListener('input', function() {
                block.html = html.value;
            });
            container.appendChild(create_field(labels.html_code, html));
        }
    };

    var create_block = function(type) {
        var block = { type: type || 'heading' };

        if(block.type === 'image') {
            block.image_id = 0;
            block.image_url = '';
            block.use_site_logo = false;
        }
        else if(block.type === 'heading' || block.type === 'description') {
            block.text = '';
        }
        else if(block.type === 'social_links' || block.type === 'contact_details') {
            block.items = [];
        }
        else if(block.type === 'navigation_menu') {
            block.menu_id = 0;
        }
        else if(block.type === 'custom_html') {
            block.html = '';
        }

        return block;
    };

    var render_block = function(section, block, block_index) {
        var card = create_element('div', 'wordpress-demo-theme-block-card');
        var header = create_element('div', 'wordpress-demo-theme-block-header');
        var select = create_element('select');

        block_types.forEach(function(type) {
            var option = create_element('option', '', labels[type]);
            option.value = type;
            option.selected = type === block.type;
            select.appendChild(option);
        });

        select.setAttribute('aria-label', labels.block_type);
        select.addEventListener('change', function() {
            section.blocks[block_index] = create_block(select.value);
            render_builder();
        });
        header.appendChild(select);

        var actions = create_element('div', 'wordpress-demo-theme-inline-actions');
        var up_button = create_icon_button(labels.move_up, 'up', function() {
            move_item(section.blocks, block_index, -1);
        });
        var down_button = create_icon_button(labels.move_down, 'down', function() {
            move_item(section.blocks, block_index, 1);
        });
        up_button.disabled = block_index === 0;
        down_button.disabled = block_index === section.blocks.length - 1;
        actions.appendChild(up_button);
        actions.appendChild(down_button);
        var remove_button = create_icon_button(labels.remove_block, 'remove', function() {
            if(window.confirm(labels.confirm_remove_block)) {
                section.blocks.splice(block_index, 1);
                render_builder();
            }
        });
        remove_button.classList.add('button-link-delete');
        actions.appendChild(remove_button);
        header.appendChild(actions);
        card.appendChild(header);

        var fields = create_element('div', 'wordpress-demo-theme-block-fields');
        render_block_fields(block, fields);
        card.appendChild(fields);
        return card;
    };

    var render_section = function(section, section_index) {
        section.blocks = Array.isArray(section.blocks) ? section.blocks : [];
        var card = create_element('section', 'wordpress-demo-theme-section-card');
        var header = create_element('div', 'wordpress-demo-theme-section-header');
        var toggle = create_button(section.name || labels.section + ' ' + (section_index + 1), 'wordpress-demo-theme-section-toggle', function() {
            section.is_open = !section.is_open;
            render_builder();
        });
        toggle.setAttribute('aria-expanded', section.is_open ? 'true' : 'false');
        toggle.setAttribute('aria-label', labels.expand);
        header.appendChild(toggle);

        var actions = create_element('div', 'wordpress-demo-theme-inline-actions');
        var up_button = create_icon_button(labels.move_up, 'up', function() {
            move_item(sections, section_index, -1);
        });
        var down_button = create_icon_button(labels.move_down, 'down', function() {
            move_item(sections, section_index, 1);
        });
        up_button.disabled = section_index === 0;
        down_button.disabled = section_index === sections.length - 1;
        actions.appendChild(up_button);
        actions.appendChild(down_button);
        var remove_button = create_icon_button(labels.remove_section, 'remove', function() {
            if(window.confirm(labels.confirm_remove_section)) {
                sections.splice(section_index, 1);
                render_builder();
            }
        });
        remove_button.classList.add('button-link-delete');
        actions.appendChild(remove_button);
        header.appendChild(actions);
        card.appendChild(header);

        if(section.is_open) {
            var content = create_element('div', 'wordpress-demo-theme-section-content');
            content.appendChild(create_field(labels.section_name, create_input(section.name, labels.section_name, function(value) {
                section.name = value;
                toggle.textContent = value || labels.section + ' ' + (section_index + 1);
            })));

            section.blocks.forEach(function(block, block_index) {
                content.appendChild(render_block(section, block, block_index));
            });

            content.appendChild(create_button(labels.add_block, 'button button-secondary', function() {
                section.blocks.push(create_block('heading'));
                render_builder();
            }));
            card.appendChild(content);
        }

        return card;
    };

    var render_builder = function() {
        builder.replaceChildren();

        if(!sections.length) {
            builder.appendChild(create_element('p', 'wordpress-demo-theme-empty-state', labels.no_sections));
            return;
        }

        sections.forEach(function(section, index) {
            builder.appendChild(render_section(section, index));
        });
    };

    var clean_sections = function() {
        return sections.map(function(section) {
            return {
                name: section.name || '',
                blocks: section.blocks.map(function(block) {
                    var clean_block = { type: block.type };

                    if(block.type === 'image') {
                        clean_block.image_id = parseInt(block.image_id, 10) || 0;
                        clean_block.use_site_logo = !!block.use_site_logo;
                    }
                    else if(block.type === 'heading' || block.type === 'description') {
                        clean_block.text = block.text || '';
                    }
                    else if(block.type === 'social_links') {
                        clean_block.items = block.items.map(function(item) {
                            return { image_id: parseInt(item.image_id, 10) || 0, url: item.url || '' };
                        });
                    }
                    else if(block.type === 'navigation_menu') {
                        clean_block.menu_id = parseInt(block.menu_id, 10) || 0;
                    }
                    else if(block.type === 'contact_details') {
                        clean_block.items = block.items.map(function(item) {
                            return {
                                image_id: parseInt(item.image_id, 10) || 0,
                                label: item.label || ''
                            };
                        });
                    }
                    else if(block.type === 'custom_html') {
                        clean_block.html = block.html || '';
                    }

                    return clean_block;
                })
            };
        });
    };

    document.querySelector('[data-add-footer-section]').addEventListener('click', function() {
        sections.forEach(function(section) {
            section.is_open = false;
        });
        sections.push({ name: '', blocks: [], is_open: true });
        render_builder();
    });

    var custom_footer_toggle = document.querySelector('[data-custom-footer-toggle]');
    var builder_card = document.querySelector('[data-footer-builder-card]');
    custom_footer_toggle.addEventListener('change', function() {
        builder_card.hidden = !custom_footer_toggle.checked;
    });

    form.addEventListener('submit', function() {
        document.querySelector('[data-footer-sections-input]').value = JSON.stringify(clean_sections());
    });

    sections.forEach(function(section) {
        section.is_open = false;
    });
    render_builder();
})();

(function() {
    'use strict';

    var create_user = function(item) {
        var row = document.createElement('li');
        var avatar = document.createElement('img');
        var content = document.createElement('span');
        var name = item.edit_url ? document.createElement('a') : document.createElement('strong');
        var name_text = document.createElement('strong');
        var time = document.createElement('small');

        row.className = 'wordpress-demo-theme-admin-interaction-user';
        avatar.src = item.avatar_url;
        avatar.alt = '';
        avatar.width = 28;
        avatar.height = 28;
        avatar.loading = 'lazy';
        name_text.textContent = item.name;

        if(item.edit_url) {
            name.href = item.edit_url;
            name.appendChild(name_text);
        }
        else {
            name.textContent = item.name;
        }

        time.textContent = item.time_label;

        if(item.exact_time) {
            time.title = item.exact_time;
        }

        content.appendChild(name);
        content.appendChild(document.createElement('br'));
        content.appendChild(time);
        row.appendChild(avatar);
        row.appendChild(content);

        return row;
    };

    document.querySelectorAll('[data-post-admin-interactions-details], [data-comment-admin-reactions-details]').forEach(function(details) {
        var loader = details.querySelector('[data-post-admin-interactions-loader], [data-comment-admin-reactions-loader]');

        if(!loader) {
            return;
        }

        var status = loader.querySelector('[data-post-admin-interactions-status]');
        var list = loader.querySelector('[data-post-admin-interactions-list]');
        var more = loader.querySelector('[data-post-admin-interactions-more]');
        var page = 1;
        var loading = false;
        var loaded = false;

        var load_users = function() {
            if(loading) {
                return;
            }

            var data = new FormData();

            loading = true;
            more.disabled = true;
            status.hidden = false;
            status.textContent = loader.getAttribute('data-loading-text');
            data.append('action', loader.getAttribute('data-ajax-action') || 'wordpress_demo_theme_admin_post_interactions');

            if(loader.hasAttribute('data-comment-id')) {
                data.append('comment_id', loader.getAttribute('data-comment-id'));
            }
            else {
                data.append('post_id', loader.getAttribute('data-post-id'));
            }

            data.append('interaction', loader.getAttribute('data-interaction'));
            data.append('nonce', loader.getAttribute('data-nonce'));
            data.append('page', page);

            window.fetch(loader.getAttribute('data-ajax-url'), {
                method: 'POST',
                credentials: 'same-origin',
                body: data
            }).then(function(response) {
                return response.json();
            }).then(function(result) {
                if(!result.success || !result.data) {
                    throw new Error('Could not load users.');
                }

                result.data.items.forEach(function(item) {
                    list.appendChild(create_user(item));
                });

                loaded = true;
                page = result.data.next_page;
                status.hidden = true;
                more.hidden = !result.data.has_more;
            }).catch(function() {
                status.hidden = false;
                status.textContent = loader.getAttribute('data-error-text');
            }).then(function() {
                loading = false;
                more.disabled = false;
            });
        };

        details.addEventListener('toggle', function() {
            if(details.open && !loaded) {
                load_users();
            }
        });

        more.addEventListener('click', load_users);
    });
})();
