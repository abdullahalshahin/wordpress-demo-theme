(function() {
    'use strict';

    var theme_storage_key = 'wordpress-demo-theme';
    var theme_light = 'light';
    var theme_dark = 'dark';
    var system_theme = window.matchMedia
        ? window.matchMedia('(prefers-color-scheme: dark)')
        : null;

    var get_system_theme = function() {
        return system_theme && system_theme.matches ? theme_dark : theme_light;
    };

    var get_saved_theme = function() {
        try {
            var saved_theme = localStorage.getItem(theme_storage_key);

            if(saved_theme === theme_light || saved_theme === theme_dark) {
                return saved_theme;
            }
        }
        catch (error) {
            return null;
        }

        return null;
    };

    var save_theme = function(theme) {
        try {
            localStorage.setItem(theme_storage_key, theme);
        }
        catch (error) {
            return;
        }
    };

    var get_initial_theme = function() {
        return get_saved_theme() || get_system_theme();
    };

    var wordpress_demo_theme = {
        get_theme_root: function() {
            return document.body;
        },

        get_saved_theme: function() {
            return get_saved_theme();
        },

        save_theme: function(theme) {
            save_theme(theme);
        },

        get_system_theme: function() {
            return get_system_theme();
        },

        update_theme_icons: function(theme) {
            var theme_toggle = document.querySelectorAll('[data-theme-toggle]');
            var moon_icons = document.querySelectorAll('[data-theme-moon]');
            var sun_icons = document.querySelectorAll('[data-theme-sun]');

            moon_icons.forEach(function(icon) {
                icon.classList.toggle('hidden', theme === theme_dark);
            });

            sun_icons.forEach(function(icon) {
                icon.classList.toggle('hidden', theme !== theme_dark);
            });

            theme_toggle.forEach(function(button) {
                button.setAttribute('aria-label', theme === theme_dark ? 'Switch to light theme' : 'Switch to dark theme');
            });
        },

        apply_theme: function(theme, should_save) {
            var theme_root = wordpress_demo_theme.get_theme_root();

            if(!theme_root) {
                return;
            }

            if(theme !== theme_light && theme !== theme_dark) {
                theme = wordpress_demo_theme.get_system_theme();
            }

            theme_root.setAttribute('data-theme', theme);
            wordpress_demo_theme.update_theme_icons(theme);

            if(should_save === true) {
                wordpress_demo_theme.save_theme(theme);
            }
        },

        theme_mode: function() {
            var theme_root = wordpress_demo_theme.get_theme_root();
            var theme_toggle = document.querySelectorAll('[data-theme-toggle]');

            if(!theme_root) {
                return;
            }

            var current_theme = theme_root.getAttribute('data-theme');

            if(current_theme !== theme_light && current_theme !== theme_dark) {
                current_theme = wordpress_demo_theme.get_saved_theme() || wordpress_demo_theme.get_system_theme();

                wordpress_demo_theme.apply_theme(current_theme, false);
            }
            else {
                wordpress_demo_theme.update_theme_icons(current_theme);
            }

            theme_toggle.forEach(function(button) {
                button.addEventListener('click', function() {
                    var active_theme = theme_root.getAttribute('data-theme') || theme_light;
                    var next_theme = active_theme === theme_dark ? theme_light : theme_dark;

                    wordpress_demo_theme.apply_theme(next_theme, true);
                });
            });

            if(!system_theme) {
                return;
            }

            var system_theme_change = function(event) {
                if(wordpress_demo_theme.get_saved_theme()) {
                    return;
                }

                wordpress_demo_theme.apply_theme(event.matches ? theme_dark : theme_light, false);
            };

            if(typeof system_theme.addEventListener === 'function') {
                system_theme.addEventListener('change', system_theme_change);
            }
            else if(typeof system_theme.addListener === 'function') {
                system_theme.addListener(system_theme_change);
            }
        },

        desktop_dropdown: function() {
            var dropdowns = document.querySelectorAll('[data-desktop-dropdown]');

            if(!dropdowns.length) {
                return;
            }

            var reset_submenu_position = function(submenu) {
                if(!submenu) {
                    return;
                }

                if(submenu.getAttribute('data-submenu-level') === 'top') {
                    submenu.classList.remove('left-0', 'right-0', 'left-auto', 'right-auto', 'translate-x-0');
                    submenu.classList.add('left-1/2', '-translate-x-1/2');
                    return;
                }

                submenu.classList.remove('right-full', 'pr-2');
                submenu.classList.add('left-full', 'pl-2');
            };

            var align_submenu = function(submenu) {
                if(!submenu || window.innerWidth < 1024) {
                    return;
                }

                var spacing = 16;

                reset_submenu_position(submenu);

                window.requestAnimationFrame(function() {
                    var rect = submenu.getBoundingClientRect();
                    var level = submenu.getAttribute('data-submenu-level');

                    if(level === 'top') {
                        if(rect.right > window.innerWidth - spacing) {
                            submenu.classList.remove('left-1/2', '-translate-x-1/2', 'left-0', 'right-auto');
                            submenu.classList.add('right-0', 'left-auto', 'translate-x-0');
                        }
                        else if(rect.left < spacing) {
                            submenu.classList.remove('left-1/2', '-translate-x-1/2', 'right-0', 'left-auto');
                            submenu.classList.add('left-0', 'right-auto', 'translate-x-0');
                        }

                        return;
                    }

                    if(rect.right > window.innerWidth - spacing) {
                        submenu.classList.remove('left-full', 'pl-2');
                        submenu.classList.add('right-full', 'pr-2');
                    }

                    rect = submenu.getBoundingClientRect();

                    if(rect.left < spacing) {
                        submenu.classList.remove('right-full', 'pr-2');
                        submenu.classList.add('left-full', 'pl-2');
                    }
                });
            };

            var align_dropdown = function(dropdown) {
                var submenu = dropdown.querySelector(':scope > [data-desktop-submenu]');

                align_submenu(submenu);
            };

            dropdowns.forEach(function(dropdown) {
                dropdown.addEventListener('mouseenter', function() {
                    dropdowns.forEach(function(other_dropdown) {
                        if(other_dropdown === dropdown) {
                            return;
                        }

                        if(document.activeElement && other_dropdown.contains(document.activeElement)) {
                            document.activeElement.blur();
                        }
                    });

                    align_dropdown(dropdown);
                });

                dropdown.addEventListener('focusin', function() {
                    align_dropdown(dropdown);
                });
            });

            document.querySelectorAll('[data-desktop-dropdown-item]').forEach(function(item) {
                item.addEventListener('mouseenter', function() {
                    align_submenu(item.querySelector(':scope > [data-desktop-submenu]'));
                });

                item.addEventListener('focusin', function() {
                    align_submenu(item.querySelector(':scope > [data-desktop-submenu]'));
                });
            });

            window.addEventListener('resize', function() {
                document.querySelectorAll('[data-desktop-submenu]').forEach(reset_submenu_position);
            });
        },

        header_scroll: function() {
            var header = document.getElementById('header');
            var mobile_menu_overlay = document.getElementById('mobileMenuOverlay');

            if(!header) {
                return;
            }

            if(header.getAttribute('data-sticky-header') !== 'true') {
                header.style.top = '';
                return;
            }

            var last_scroll = Math.max(window.scrollY, 0);
            var scroll_down_distance = 0;
            var scroll_up_distance = 0;
            var ticking = false;
            var hide_threshold = 12;
            var show_threshold = 6;
            var top_threshold = 80;

            var show_header = function() {
                header.style.top = '0px';
            };

            var hide_header = function() {
                header.style.top = '-' + header.offsetHeight + 'px';
            };

            var update_header = function() {
                var current_scroll = Math.max(window.scrollY, 0);
                var mobile_menu_open = mobile_menu_overlay && mobile_menu_overlay.getAttribute('aria-hidden') === 'false';

                if(mobile_menu_open || current_scroll <= top_threshold) {
                    show_header();
                    scroll_down_distance = 0;
                    scroll_up_distance = 0;
                    last_scroll = current_scroll;
                    ticking = false;

                    return;
                }

                if(current_scroll > last_scroll) {
                    scroll_down_distance += current_scroll - last_scroll;
                    scroll_up_distance = 0;

                    if(scroll_down_distance >= hide_threshold) {
                        hide_header();
                        scroll_down_distance = 0;
                    }
                }
                else if(current_scroll < last_scroll) {
                    scroll_up_distance += last_scroll - current_scroll;
                    scroll_down_distance = 0;

                    if(scroll_up_distance >= show_threshold) {
                        show_header();
                        scroll_up_distance = 0;
                    }
                }

                last_scroll = current_scroll;
                ticking = false;
            };

            window.addEventListener(
                'scroll',
                function() {
                    if(ticking) {
                        return;
                    }

                    ticking = true;
                    window.requestAnimationFrame(update_header);
                },
                { passive: true }
            );

            window.addEventListener('resize', function() {
                show_header();
                last_scroll = Math.max(window.scrollY, 0);
                scroll_down_distance = 0;
                scroll_up_distance = 0;
            });
        },

        mobile_menu: function() {
            var mobile_menu_toggle = document.getElementById('mobileMenuToggle');
            var mobile_menu_overlay = document.getElementById('mobileMenuOverlay');
            var mobile_menu_backdrop = document.getElementById('mobileMenuBackdrop');
            var mobile_menu_panel = document.getElementById('mobileMenuPanel');
            var mobile_menu_close = document.getElementById('mobileMenuClose');
            var header = document.getElementById('header');
            var mobile_menu_timeout = null;

            if(!mobile_menu_toggle || !mobile_menu_overlay || !mobile_menu_backdrop || !mobile_menu_panel || !mobile_menu_close) {
                return;
            }

            var close_accordions = function() {
                mobile_menu_panel
                    .querySelectorAll('details')
                    .forEach(function(details) {
                        details.removeAttribute('open');
                    });
            };

            var open_mobile_menu = function() {
                if(mobile_menu_timeout) {
                    window.clearTimeout(mobile_menu_timeout);
                    mobile_menu_timeout = null;
                }

                if(header) {
                    header.style.top = '0px';
                }

                mobile_menu_overlay.classList.remove('hidden');
                mobile_menu_overlay.setAttribute('aria-hidden', 'false');
                mobile_menu_toggle.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';

                window.requestAnimationFrame(function() {
                    mobile_menu_backdrop.classList.remove('opacity-0');
                    mobile_menu_backdrop.classList.add('opacity-100');
                    mobile_menu_panel.classList.remove('-translate-x-full');
                    mobile_menu_panel.classList.add('translate-x-0');
                    mobile_menu_close.focus();
                });
            };

            var close_mobile_menu = function(return_focus) {
                mobile_menu_backdrop.classList.remove('opacity-100');
                mobile_menu_backdrop.classList.add('opacity-0');
                mobile_menu_panel.classList.remove('translate-x-0');
                mobile_menu_panel.classList.add('-translate-x-full');
                mobile_menu_toggle.setAttribute('aria-expanded', 'false');
                mobile_menu_overlay.setAttribute('aria-hidden', 'true');

                if(mobile_menu_timeout) {
                    window.clearTimeout(mobile_menu_timeout);
                }

                mobile_menu_timeout = window.setTimeout(function() {
                    mobile_menu_overlay.classList.add('hidden');
                    document.body.style.overflow = '';
                    close_accordions();

                    if(return_focus !== false && window.innerWidth < 1024) {
                        mobile_menu_toggle.focus();
                    }

                    mobile_menu_timeout = null;
                }, 200);
            };

            var trap_mobile_focus = function(event) {
                if(event.key !== 'Tab' || mobile_menu_overlay.classList.contains('hidden')) {
                    return;
                }

                var focusable_elements = mobile_menu_panel.querySelectorAll(
                    'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
                );

                if(!focusable_elements.length) {
                    return;
                }

                var first_element = focusable_elements[0];
                var last_element = focusable_elements[focusable_elements.length - 1];

                if(event.shiftKey && document.activeElement === first_element) {
                    event.preventDefault();
                    last_element.focus();
                }
                else if(!event.shiftKey && document.activeElement === last_element) {
                    event.preventDefault();
                    first_element.focus();
                }
            };

            mobile_menu_toggle.addEventListener('click', open_mobile_menu);

            mobile_menu_close.addEventListener('click', function() {
                close_mobile_menu(true);
            });

            mobile_menu_backdrop.addEventListener('click', function() {
                close_mobile_menu(true);
            });

            mobile_menu_panel.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    close_mobile_menu(false);
                });
            });

            document.addEventListener('keydown', function(event) {
                if(event.key === 'Escape' && !mobile_menu_overlay.classList.contains('hidden')) {
                    close_mobile_menu(true);
                    return;
                }

                trap_mobile_focus(event);
            });

            window.addEventListener('resize', function() {
                if(window.innerWidth >= 1024 && !mobile_menu_overlay.classList.contains('hidden')) {
                    close_mobile_menu(false);
                }
            });
        },

        mobile_accordion: function() {
            var accordion_toggle = document.querySelectorAll('[data-mobile-accordion]');

            if(!accordion_toggle.length) {
                return;
            }

            var close_accordion = function(button) {
                var content = button.nextElementSibling;
                var arrow = button.querySelector('[data-mobile-arrow]');

                button.setAttribute('aria-expanded', 'false');

                if(content) {
                    content.style.maxHeight = '0px';
                    content.classList.add('opacity-0');
                }

                if(arrow) {
                    arrow.classList.remove('rotate-180');
                }
            };

            var open_accordion = function(button) {
                var content = button.nextElementSibling;
                var arrow = button.querySelector('[data-mobile-arrow]');

                button.setAttribute('aria-expanded', 'true');

                if(content) {
                    content.style.maxHeight = content.scrollHeight + 'px';
                    content.classList.remove('opacity-0');
                }

                if(arrow) {
                    arrow.classList.add('rotate-180');
                }
            };

            accordion_toggle.forEach(function(button) {
                button.addEventListener('click', function() {
                    var is_open = button.getAttribute('aria-expanded') === 'true';

                    accordion_toggle.forEach(function(other_button) {
                        if(other_button !== button) {
                            close_accordion(other_button);
                        }
                    });

                    if(is_open) {
                        close_accordion(button);
                    }
                    else {
                        open_accordion(button);
                    }
                });
            });

            window.addEventListener('resize', function() {
                accordion_toggle.forEach(function(button) {
                    if(button.getAttribute('aria-expanded') !== 'true') {
                        return;
                    }

                    var content = button.nextElementSibling;

                    if(content) {
                        content.style.maxHeight = content.scrollHeight + 'px';
                    }
                });
            });
        },

        back_to_top: function() {
            var button = document.querySelector('[data-back-to-top]');

            if(!button) {
                return;
            }

            var toggle_button = function() {
                var should_show = window.scrollY > 320;

                button.classList.toggle('pointer-events-none', !should_show);
                button.classList.toggle('opacity-0', !should_show);
                button.classList.toggle('translate-y-3', !should_show);
            };

            button.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            window.addEventListener('scroll', toggle_button, { passive: true });
            toggle_button();
        },

        search_modal: function() {
            var search_modal = document.getElementById('search-modal');
            var search_modal_panel = document.getElementById('search-modal-panel');
            var search_modal_input = document.getElementById('search-modal-input');
            var search_modal_close = document.getElementById('search-modal-close');
            var search_results = document.querySelector('[data-search-results]');
            var search_open_buttons = document.querySelectorAll('[data-search-open]');
            var last_focused_element = null;
            var search_timeout = null;
            var search_controller = null;

            if(!search_modal || !search_modal_panel || !search_open_buttons.length) {
                return;
            }

            var default_results_html = search_results ? search_results.innerHTML : '';
            var search_endpoint = search_results ? search_results.getAttribute('data-search-endpoint') : '';
            var search_url = search_results ? search_results.getAttribute('data-search-url') : '';
            var suggestions_title = search_results ? search_results.getAttribute('data-suggestions-title') : 'Suggestions';
            var loading_text = search_results ? search_results.getAttribute('data-loading-text') : 'Searching...';
            var empty_text = search_results ? search_results.getAttribute('data-empty-text') : 'No suggestions found.';

            var decode_html_entities = function(value) {
                var textarea = document.createElement('textarea');

                textarea.innerHTML = String(value || '');

                return textarea.value;
            };

            var create_search_icon = function() {
                var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');

                svg.setAttribute('class', 'size-5 flex-none text-[var(--color-text-muted)]');
                svg.setAttribute('fill', 'none');
                svg.setAttribute('viewBox', '0 0 24 24');
                svg.setAttribute('stroke-width', '1.8');
                svg.setAttribute('stroke', 'currentColor');
                svg.setAttribute('aria-hidden', 'true');

                path.setAttribute('stroke-linecap', 'round');
                path.setAttribute('stroke-linejoin', 'round');
                path.setAttribute('d', 'm21 21-4.35-4.35m1.35-5.4a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z');

                svg.appendChild(path);

                return svg;
            };

            var create_results_group = function(title) {
                var item = document.createElement('li');
                var heading = document.createElement('h2');
                var list = document.createElement('ul');

                item.className = 'p-2';
                heading.className = 'mt-4 mb-2 px-3 text-xs font-medium text-[var(--color-text-muted)]';
                heading.textContent = title;
                list.className = 'text-sm text-[var(--color-text-secondary)]';

                item.appendChild(heading);
                item.appendChild(list);

                return {
                    item: item,
                    list: list
                };
            };

            var render_search_message = function(message) {
                var group = create_results_group(suggestions_title);
                var item = document.createElement('li');

                if(!search_results) {
                    return;
                }

                item.className = 'px-3 py-2 text-sm text-[var(--color-text-muted)]';
                item.textContent = message;
                group.list.appendChild(item);
                search_results.replaceChildren(group.item);
            };

            var render_search_results = function(items) {
                var group = create_results_group(suggestions_title);

                if(!search_results) {
                    return;
                }

                if(!items.length) {
                    render_search_message(empty_text);
                    return;
                }

                items.forEach(function(item) {
                    var result_item = document.createElement('li');
                    var link = document.createElement('a');
                    var title = document.createElement('span');
                    var type = document.createElement('span');
                    var content = document.createElement('span');
                    var item_type = item.subtype || item.type || '';

                    link.href = item.url || search_url;
                    link.className = 'flex cursor-pointer items-center rounded-md px-3 py-2 transition hover:bg-[var(--color-surface-hover)] hover:text-[var(--color-text-primary)]';

                    content.className = 'ms-3 min-w-0 flex-auto';
                    title.className = 'block truncate';
                    title.textContent = decode_html_entities(item.title);
                    type.className = 'mt-0.5 block text-xs capitalize text-[var(--color-text-muted)]';
                    type.textContent = item_type.replace(/_/g, ' ');

                    content.appendChild(title);

                    if(item_type) {
                        content.appendChild(type);
                    }

                    link.appendChild(create_search_icon());
                    link.appendChild(content);
                    result_item.appendChild(link);
                    group.list.appendChild(result_item);
                });

                search_results.replaceChildren(group.item);
            };

            var fetch_search_results = function(query) {
                var url = null;

                if(!search_results || !search_endpoint || !window.fetch || !window.URL) {
                    return;
                }

                if(search_controller) {
                    search_controller.abort();
                }

                search_controller = window.AbortController ? new AbortController() : null;
                url = new URL(search_endpoint);
                url.searchParams.set('search', query);
                url.searchParams.set('per_page', '8');
                url.searchParams.set('type', 'post');
                url.searchParams.append('subtype[]', 'post');
                url.searchParams.set('_fields', 'title,url,type,subtype');

                render_search_message(loading_text);

                window.fetch(url.toString(), {
                    signal: search_controller ? search_controller.signal : undefined
                })
                    .then(function(response) {
                        if(!response.ok) {
                            throw new Error('Search request failed.');
                        }

                        return response.json();
                    })
                    .then(function(items) {
                        render_search_results(Array.isArray(items) ? items : []);
                    })
                    .catch(function(error) {
                        if(error.name === 'AbortError') {
                            return;
                        }

                        render_search_message(empty_text);
                    });
            };

            var reset_search_results = function() {
                if(search_results) {
                    search_results.innerHTML = default_results_html;
                }
            };

            var open_search_modal = function(trigger) {
                last_focused_element = trigger || document.activeElement;

                search_modal.classList.remove('pointer-events-none','opacity-0');
                search_modal.classList.add('pointer-events-auto','opacity-100');
                search_modal_panel.classList.remove('translate-y-6','scale-[0.98]','opacity-0');
                search_modal_panel.classList.add('translate-y-0','scale-100','opacity-100');

                search_modal.setAttribute('aria-hidden', 'false');

                search_open_buttons.forEach(function(button) {
                    button.setAttribute('aria-expanded', 'true');
                });

                document.body.style.overflow = 'hidden';

                window.requestAnimationFrame(function() {
                    if(search_modal_input) {
                        search_modal_input.focus();
                    }
                });
            };

            var close_search_modal = function() {
                search_modal.classList.remove('pointer-events-auto','opacity-100');
                search_modal.classList.add('pointer-events-none','opacity-0');
                search_modal_panel.classList.remove('translate-y-0','scale-100','opacity-100');
                search_modal_panel.classList.add('translate-y-6','scale-[0.98]','opacity-0');

                search_modal.setAttribute('aria-hidden', 'true');

                search_open_buttons.forEach(function(button) {
                    button.setAttribute('aria-expanded', 'false');
                });

                document.body.style.overflow = '';

                if(last_focused_element && typeof last_focused_element.focus === 'function') {
                    last_focused_element.focus();
                }
            };

            search_open_buttons.forEach(function(button) {
                button.addEventListener('click', function() {
                    open_search_modal(button);
                });
            });

            if(search_modal_close) {
                search_modal_close.addEventListener(
                    'click',
                    close_search_modal
                );
            }

            if(search_modal_input && search_results) {
                search_modal_input.addEventListener('input', function() {
                    var query = search_modal_input.value.trim();

                    window.clearTimeout(search_timeout);

                    if(search_controller) {
                        search_controller.abort();
                    }

                    if(query.length < 2) {
                        reset_search_results();
                        return;
                    }

                    search_timeout = window.setTimeout(function() {
                        fetch_search_results(query);
                    }, 400);
                });
            }

            search_modal.addEventListener('click', function(event) {
                if(event.target === search_modal) {
                    close_search_modal();
                }
            });

            document.addEventListener('keydown', function(event) {
                if(event.key === 'Escape' && search_modal.getAttribute('aria-hidden') === 'false') {
                    close_search_modal();
                }
            });
        },

        post_share: function() {
            document.querySelectorAll('[data-post-share-button]').forEach(function(button, index) {
                var container = button.parentElement;
                var menu = container.querySelector('[data-post-share-menu]');

                if(!menu) {
                    return;
                }

                menu.id = menu.id || 'post-share-menu-' + index;
                button.setAttribute('aria-controls', menu.id);

                var close_menu = function() {
                    menu.classList.add('hidden');
                    button.setAttribute('aria-expanded', 'false');
                };

                button.addEventListener('click', function() {
                    var is_open = button.getAttribute('aria-expanded') === 'true';

                    menu.classList.toggle('hidden', is_open);
                    button.setAttribute('aria-expanded', is_open ? 'false' : 'true');
                });

                document.addEventListener('click', function(event) {
                    if(!container.contains(event.target)) {
                        close_menu();
                    }
                });

                document.addEventListener('keydown', function(event) {
                    if(event.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') {
                        close_menu();
                        button.focus();
                    }
                });

                container.addEventListener('focusout', function(event) {
                    if(!container.contains(event.relatedTarget)) {
                        close_menu();
                    }
                });
            });

            document.querySelectorAll('[data-copy-post-link]').forEach(function(button) {
                var label = button.querySelector('[data-copy-post-link-text]');
                var original_text = label ? label.textContent : '';
                var feedback_timeout = null;

                var fallback_copy = function(url) {
                    var input = document.createElement('textarea');
                    var copied = false;

                    input.value = url;
                    input.setAttribute('readonly', '');
                    input.style.position = 'fixed';
                    input.style.opacity = '0';
                    button.parentElement.appendChild(input);

                    try {
                        input.focus();
                        input.select();
                        copied = document.execCommand('copy');
                    }
                    catch (error) {
                        copied = false;
                    }
                    finally {
                        input.remove();
                        button.focus();
                    }

                    return copied;
                };

                var show_feedback = function(copied) {
                    if(!label) {
                        return;
                    }

                    window.clearTimeout(feedback_timeout);
                    label.textContent = button.getAttribute(copied ? 'data-copied-text' : 'data-copy-error-text') || original_text;
                    feedback_timeout = window.setTimeout(function() {
                        label.textContent = original_text;
                    }, 2500);
                };

                button.addEventListener('click', function() {
                    var url = button.getAttribute('data-post-url');

                    if(!url) {
                        show_feedback(false);
                        return;
                    }

                    if(navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
                        navigator.clipboard.writeText(url).then(function() {
                            show_feedback(true);
                        }).catch(function() {
                            show_feedback(fallback_copy(url));
                        });
                    }
                    else {
                        show_feedback(fallback_copy(url));
                    }
                });
            });
        },

        post_interactions: function() {
            var compact_number = function(number) {
                number = Math.max(0, Number(number) || 0);

                if(number < 1000) {
                    return number.toLocaleString(document.documentElement.lang || undefined);
                }

                var divisor = number >= 1000000 ? 1000000 : 1000;
                var suffix = number >= 1000000 ? 'M' : 'K';
                var value = number / divisor;
                var decimals = value < 10 && Math.floor(value) !== value ? 1 : 0;

                return value.toLocaleString(document.documentElement.lang || undefined, {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                }) + suffix;
            };

            document.querySelectorAll('[data-post-interactions]').forEach(function(group) {
                var buttons = group.querySelectorAll('[data-post-interaction]');
                var status = group.querySelector('[data-post-interaction-status]');

                buttons.forEach(function(button) {
                    button.addEventListener('click', function() {
                        var data = new FormData();

                        data.append('action', 'wordpress_demo_theme_post_interaction');
                        data.append('post_id', group.getAttribute('data-post-id'));
                        data.append('nonce', group.getAttribute('data-interaction-nonce'));
                        data.append('interaction', button.getAttribute('data-post-interaction'));
                        status.textContent = '';
                        group.setAttribute('aria-busy', 'true');
                        buttons.forEach(function(item) { item.disabled = true; });

                        window.fetch(group.getAttribute('data-interaction-url'), {
                            method: 'POST',
                            credentials: 'same-origin',
                            body: data
                        }).then(function(response) {
                            return response.json();
                        }).then(function(result) {
                            if(!result.success || !result.data) {
                                if(result.data && result.data.login_url) {
                                    window.location.href = result.data.login_url;
                                    return;
                                }

                                throw new Error(result.data && result.data.message ? result.data.message : 'Post interaction failed.');
                            }

                            buttons.forEach(function(item) {
                                var type = item.getAttribute('data-post-interaction');
                                var active = type === 'like' ? result.data.liked : result.data.saved;
                                var icon = item.querySelector('svg');

                                item.setAttribute('aria-pressed', active ? 'true' : 'false');
                                item.setAttribute('aria-label', item.getAttribute(active ? 'data-active-label' : 'data-inactive-label'));

                                if(icon) {
                                    icon.setAttribute('fill', active ? 'currentColor' : 'none');
                                }
                            });

                            var like_count = group.querySelector('[data-post-like-count]');

                            if(like_count) {
                                like_count.setAttribute('data-count', result.data.like_count);
                                like_count.textContent = compact_number(result.data.like_count);
                            }
                        }).catch(function(error) {
                            status.textContent = error.message || group.getAttribute('data-interaction-error');
                        }).then(function() {
                            group.setAttribute('aria-busy', 'false');
                            buttons.forEach(function(item) { item.disabled = false; });
                        });
                    });
                });
            });
        },

        comment_actions: function() {
            var menus = document.querySelectorAll('[data-comment-actions]');

            document.addEventListener('click', function(event) {
                menus.forEach(function(menu) {
                    if(!menu.contains(event.target)) {
                        menu.open = false;
                    }
                });
            });

            document.addEventListener('keydown', function(event) {
                if(event.key !== 'Escape') {
                    return;
                }

                menus.forEach(function(menu) {
                    if(menu.open) {
                        menu.open = false;
                        menu.querySelector('summary').focus();
                    }
                });
            });

            document.querySelectorAll('[data-comment-reactions]').forEach(function(group) {
                var buttons = group.querySelectorAll('[data-comment-reaction]');
                var status = group.querySelector('[data-reaction-status]');

                buttons.forEach(function(button) {
                    button.addEventListener('click', function() {
                        var reaction = button.getAttribute('aria-pressed') === 'true' ? 'none' : button.getAttribute('data-comment-reaction');
                        var data = new FormData();

                        data.append('action', 'wordpress_demo_theme_comment_reaction');
                        data.append('comment_id', group.getAttribute('data-comment-reactions'));
                        data.append('nonce', group.getAttribute('data-reaction-nonce'));
                        data.append('reaction', reaction);
                        status.textContent = '';
                        status.classList.add('hidden');
                        group.setAttribute('aria-busy', 'true');
                        buttons.forEach(function(item) { item.disabled = true; });

                        window.fetch(group.getAttribute('data-reaction-url'), {
                            method: 'POST',
                            credentials: 'same-origin',
                            body: data
                        }).then(function(response) {
                            return response.json();
                        }).then(function(result) {
                            if(!result.success || !result.data) {
                                throw new Error('Reaction request failed.');
                            }

                            buttons.forEach(function(item) {
                                var type = item.getAttribute('data-comment-reaction');
                                var count = item.querySelector('[data-reaction-count]');
                                var icon = item.querySelector('svg');
                                var selected = result.data.selected === type;

                                item.setAttribute('aria-pressed', selected ? 'true' : 'false');

                                if(icon) {
                                    icon.setAttribute('fill', selected ? 'currentColor' : 'none');
                                }

                                count.textContent = Number(result.data[type] || 0).toLocaleString(document.documentElement.lang || undefined);
                            });
                        }).catch(function() {
                            status.textContent = group.getAttribute('data-reaction-error');
                            status.classList.remove('hidden');
                        }).then(function() {
                            group.setAttribute('aria-busy', 'false');
                            buttons.forEach(function(item) { item.disabled = false; });
                        });
                    });
                });
            });
        },

        table_of_contents: function() {
            var toc_card = document.querySelector('[data-table-of-contents-card]');
            var toc_list = document.querySelector('[data-table-of-contents]');
            var mobile_toc_list = document.querySelector('[data-mobile-table-of-contents]');
            var mobile_toc_toggle = document.querySelector('[data-mobile-toc-toggle]');
            var mobile_toc_drawer = document.querySelector('[data-mobile-toc-drawer]');
            var mobile_toc_overlay = document.querySelector('[data-mobile-toc-overlay]');
            var mobile_toc_close = document.querySelector('[data-mobile-toc-close]');
            var post_content = document.querySelector('.wordpress-demo-theme-content');
            var header = document.getElementById('header');
            var blog_sidebar = null;
            var desktop_toc_query = window.matchMedia('(min-width: 1024px)');
            var toc_scroll_ticking = false;
            var sticky_frame = null;
            var sticky_animation_frame = null;

            if(!toc_card || !toc_list || !mobile_toc_list || !post_content) {
                return;
            }

            var headings = Array.prototype.slice.call(post_content.querySelectorAll('h2'));

            if(!headings.length) {
                return;
            }

            var create_slug = function(text, index) {
                var slug = text
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');

                if(/^[0-9]/.test(slug)) {
                    slug = 'section-' + slug;
                }

                return slug || 'section-' + (index + 1);
            };

            var create_toc_link = function(heading, index, is_mobile) {
                var link = document.createElement('a');
                var number = document.createElement('span');
                var title = document.createElement('span');
                var arrow = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                var heading_text = heading.textContent.trim();

                link.href = '#' + heading.id;
                link.className = (is_mobile ? 'mobile-toc-link ' : 'toc-link ') + 'group relative flex items-center gap-3 rounded-xl px-2 py-1.5 transition-all duration-200 hover:bg-[var(--color-primary-subtle)]';
                link.dataset.section = heading.id;
                link.dataset.active = index === 0 ? 'true' : 'false';

                number.className = 'relative z-10 flex size-7 shrink-0 items-center justify-center rounded-full border border-[var(--color-border)] bg-[var(--color-card)] text-[11px] font-bold text-[var(--color-text-muted)] transition-all duration-200 group-hover:border-[var(--color-primary)] group-hover:bg-[var(--color-primary)] group-hover:text-[var(--color-primary-text)] group-data-[active=true]:border-[var(--color-primary)] group-data-[active=true]:bg-[var(--color-primary)] group-data-[active=true]:text-[var(--color-primary-text)]';
                number.textContent = String(index + 1).padStart(2, '0');

                title.className = 'min-w-0 flex-1 text-sm font-medium text-[var(--color-text-secondary)] transition-colors duration-200 group-hover:text-[var(--color-primary)] group-data-[active=true]:font-semibold group-data-[active=true]:text-[var(--color-primary)]';
                title.textContent = heading_text;

                arrow.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
                arrow.setAttribute('viewBox', '0 0 20 20');
                arrow.setAttribute('fill', 'none');
                arrow.setAttribute('stroke', 'currentColor');
                arrow.setAttribute('stroke-width', '1.6');
                arrow.setAttribute('class', 'size-4 shrink-0 text-[var(--color-text-muted)] opacity-0 transition-all duration-200 group-hover:translate-x-0.5 group-hover:text-[var(--color-primary)] group-hover:opacity-100 group-data-[active=true]:translate-x-0.5 group-data-[active=true]:text-[var(--color-primary)] group-data-[active=true]:opacity-100');
                path.setAttribute('stroke-linecap', 'round');
                path.setAttribute('stroke-linejoin', 'round');
                path.setAttribute('d', 'm7 4.5 5.5 5.5L7 15.5');

                arrow.appendChild(path);
                link.appendChild(number);
                link.appendChild(title);
                link.appendChild(arrow);

                return link;
            };

            headings.forEach(function(heading, index) {
                var heading_text = heading.textContent.trim();

                if(!heading_text) {
                    return;
                }

                if(!heading.id) {
                    heading.id = create_slug(heading_text, index);
                }

                heading.classList.add('toc-section', 'scroll-mt-24');
                toc_list.appendChild(create_toc_link(heading, index, false));
                mobile_toc_list.appendChild(create_toc_link(heading, index, true));
            });

            if(!toc_list.children.length) {
                return;
            }

            var toc_links = document.querySelectorAll('.toc-link');
            var mobile_toc_links = document.querySelectorAll('.mobile-toc-link');
            var toc_sections = document.querySelectorAll('.toc-section');

            var open_toc = function() {
                if(!mobile_toc_drawer || !mobile_toc_overlay || !mobile_toc_toggle) {
                    return;
                }

                if(desktop_toc_query.matches) {
                    return;
                }

                mobile_toc_drawer.classList.remove('pointer-events-none', 'invisible', 'translate-y-2', 'scale-95', 'opacity-0');
                mobile_toc_drawer.classList.add('pointer-events-auto', 'visible', 'translate-y-0', 'scale-100', 'opacity-100');
                mobile_toc_overlay.classList.remove('pointer-events-none', 'invisible', 'opacity-0');
                mobile_toc_overlay.classList.add('pointer-events-auto', 'visible', 'opacity-100');
                mobile_toc_toggle.setAttribute('aria-expanded', 'true');
            };

            var close_toc = function() {
                if(!mobile_toc_drawer || !mobile_toc_overlay || !mobile_toc_toggle) {
                    return;
                }

                mobile_toc_drawer.classList.remove('pointer-events-auto', 'visible', 'translate-y-0', 'scale-100', 'opacity-100');
                mobile_toc_drawer.classList.add('pointer-events-none', 'invisible', 'translate-y-2', 'scale-95', 'opacity-0');
                mobile_toc_overlay.classList.remove('pointer-events-auto', 'visible', 'opacity-100');
                mobile_toc_overlay.classList.add('pointer-events-none', 'invisible', 'opacity-0');
                mobile_toc_toggle.setAttribute('aria-expanded', 'false');
            };

            var sync_mobile_toc_visibility = function() {
                if(!mobile_toc_toggle || !mobile_toc_drawer || !mobile_toc_overlay) {
                    return;
                }

                if(desktop_toc_query.matches) {
                    close_toc();
                    mobile_toc_toggle.classList.add('hidden');
                    mobile_toc_toggle.classList.remove('flex');
                    return;
                }

                mobile_toc_toggle.classList.remove('hidden');
                mobile_toc_toggle.classList.add('flex');
            };

            var set_current_toc_section = function(section_id) {
                toc_links.forEach(function(link) {
                    link.dataset.active = link.dataset.section === section_id ? 'true' : 'false';
                });

                mobile_toc_links.forEach(function(link) {
                    link.dataset.active = link.dataset.section === section_id ? 'true' : 'false';
                });
            };

            var update_current_toc_section = function() {
                var current_section = toc_sections[0];

                toc_sections.forEach(function(section) {
                    if(section.getBoundingClientRect().top <= 140) {
                        current_section = section;
                    }
                });

                if(current_section) {
                    set_current_toc_section(current_section.id);
                }
            };

            var request_toc_update = function() {
                if(toc_scroll_ticking) {
                    return;
                }

                toc_scroll_ticking = true;

                window.requestAnimationFrame(function() {
                    update_current_toc_section();
                    toc_scroll_ticking = false;
                });
            };

            var get_header_visible_height = function() {
                if(!header) {
                    return 0;
                }

                var header_rect = header.getBoundingClientRect();

                return Math.max(0, Math.min(header_rect.height, header_rect.bottom));
            };

            var sync_blog_sidebar = function() {
                if(!blog_sidebar) {
                    return;
                }

                if(window.innerWidth < 1024) {
                    blog_sidebar.style.top = '';
                    return;
                }

                blog_sidebar.style.top = Math.round(get_header_visible_height() + 16) + 'px';
            };

            var request_sidebar_sync = function() {
                if(sticky_frame) {
                    return;
                }

                sticky_frame = window.requestAnimationFrame(function() {
                    sync_blog_sidebar();
                    sticky_frame = null;
                });
            };

            var sync_sidebar_during_header_animation = function() {
                if(sticky_animation_frame) {
                    window.cancelAnimationFrame(sticky_animation_frame);
                }

                var start_time = performance.now();

                var animate_sync = function(current_time) {
                    sync_blog_sidebar();

                    if(current_time - start_time < 380) {
                        sticky_animation_frame = window.requestAnimationFrame(animate_sync);
                        return;
                    }

                    sticky_animation_frame = null;
                    sync_blog_sidebar();
                };

                sticky_animation_frame = window.requestAnimationFrame(animate_sync);
            };

            var scroll_to_section = function(link) {
                var target_id = link.getAttribute('href');
                var target_hash = target_id && target_id.indexOf('#') === 0 ? target_id.substring(1) : '';
                var target = null;

                if(target_hash) {
                    try {
                        target_hash = decodeURIComponent(target_hash);
                    } catch(error) {
                        target_hash = target_hash;
                    }

                    target = document.getElementById(target_hash);
                }

                if(!target) {
                    return;
                }

                window.scrollTo({
                    top: target.getBoundingClientRect().top + window.scrollY - 96,
                    behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'
                });

                history.replaceState(null, '', target_id);
            };

            toc_links.forEach(function(link) {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    set_current_toc_section(link.dataset.section);
                    scroll_to_section(link);
                });
            });

            mobile_toc_links.forEach(function(link) {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    set_current_toc_section(link.dataset.section);
                    close_toc();
                    scroll_to_section(link);
                });
            });

            if(mobile_toc_toggle && mobile_toc_drawer && mobile_toc_overlay && mobile_toc_close) {
                sync_mobile_toc_visibility();

                mobile_toc_toggle.addEventListener('click', function() {
                    if(mobile_toc_toggle.getAttribute('aria-expanded') === 'true') {
                        close_toc();
                        return;
                    }

                    open_toc();
                });

                mobile_toc_close.addEventListener('click', close_toc);
                mobile_toc_overlay.addEventListener('click', close_toc);
            }

            window.addEventListener('scroll', request_toc_update, { passive: true });
            window.addEventListener('scroll', request_sidebar_sync, { passive: true });
            window.addEventListener('resize', function() {
                request_toc_update();
                sync_mobile_toc_visibility();
                sync_blog_sidebar();
                sync_sidebar_during_header_animation();
            });

            if(typeof desktop_toc_query.addEventListener === 'function') {
                desktop_toc_query.addEventListener('change', sync_mobile_toc_visibility);
            }
            else if(typeof desktop_toc_query.addListener === 'function') {
                desktop_toc_query.addListener(sync_mobile_toc_visibility);
            }

            if(header) {
                var header_observer = new MutationObserver(sync_sidebar_during_header_animation);

                header_observer.observe(header, {
                    attributes: true,
                    attributeFilter: ['style', 'class']
                });

                header.addEventListener('transitionrun', sync_sidebar_during_header_animation);
                header.addEventListener('transitionend', sync_blog_sidebar);
            }

            document.addEventListener('keydown', function(event) {
                if(event.key === 'Escape') {
                    close_toc();
                }
            });

            toc_card.hidden = false;
            sync_mobile_toc_visibility();
            sync_blog_sidebar();
            update_current_toc_section();
        },

        single_post_sidebar: function() {
            var header = document.getElementById('header');
            var blog_sidebar = document.getElementById('single-post-sidebar');
            var footer = document.querySelector('footer');
            var sidebar_parent = blog_sidebar ? blog_sidebar.parentElement : null;
            var sticky_frame = null;
            var sticky_animation_frame = null;

            if(!blog_sidebar || !sidebar_parent) {
                return;
            }

            if(window.getComputedStyle(sidebar_parent).position === 'static') {
                sidebar_parent.style.position = 'relative';
            }

            var get_header_visible_height = function() {
                if(!header) {
                    return 0;
                }

                var header_rect = header.getBoundingClientRect();

                return Math.max(0, Math.min(header_rect.height, header_rect.bottom));
            };

            var reset_blog_sidebar = function() {
                blog_sidebar.style.position = '';
                blog_sidebar.style.top = '0px';
                blog_sidebar.style.left = '';
                blog_sidebar.style.width = '';
                blog_sidebar.style.zIndex = '';
            };

            var sync_blog_sidebar = function() {
                if(window.innerWidth < 1024) {
                    reset_blog_sidebar();
                    return;
                }

                reset_blog_sidebar();
                blog_sidebar.style.position = 'relative';

                var top_offset = Math.round(get_header_visible_height() + 16);
                var parent_rect = sidebar_parent.getBoundingClientRect();
                var sidebar_rect = blog_sidebar.getBoundingClientRect();
                var parent_top = parent_rect.top + window.scrollY;
                var fixed_start = parent_top - top_offset;
                var current_scroll = window.scrollY;
                var footer_top = footer ? footer.getBoundingClientRect().top : window.innerHeight;
                var footer_document_top = footer ? footer_top + window.scrollY : 0;
                var sidebar_height = blog_sidebar.offsetHeight;
                var bottom_gap = 24;
                var sidebar_bottom = top_offset + sidebar_height + bottom_gap;

                if(current_scroll < fixed_start) {
                    return;
                }

                if(footer && footer_top <= sidebar_bottom) {
                    blog_sidebar.style.position = 'absolute';
                    blog_sidebar.style.top = Math.max(0, footer_document_top - parent_top - sidebar_height - bottom_gap) + 'px';
                    blog_sidebar.style.left = Math.max(0, sidebar_rect.left - parent_rect.left) + 'px';
                    blog_sidebar.style.width = sidebar_rect.width + 'px';
                    return;
                }

                blog_sidebar.style.position = 'fixed';
                blog_sidebar.style.top = top_offset + 'px';
                blog_sidebar.style.left = sidebar_rect.left + 'px';
                blog_sidebar.style.width = sidebar_rect.width + 'px';
                blog_sidebar.style.zIndex = '20';
            };

            var request_sidebar_sync = function() {
                if(sticky_frame) {
                    return;
                }

                sticky_frame = window.requestAnimationFrame(function() {
                    sync_blog_sidebar();
                    sticky_frame = null;
                });
            };

            var sync_sidebar_during_header_animation = function() {
                if(sticky_animation_frame) {
                    window.cancelAnimationFrame(sticky_animation_frame);
                }

                var start_time = performance.now();

                var animate_sync = function(current_time) {
                    sync_blog_sidebar();

                    if(current_time - start_time < 380) {
                        sticky_animation_frame = window.requestAnimationFrame(animate_sync);
                        return;
                    }

                    sticky_animation_frame = null;
                    sync_blog_sidebar();
                };

                sticky_animation_frame = window.requestAnimationFrame(animate_sync);
            };

            window.addEventListener('scroll', request_sidebar_sync, { passive: true });
            window.addEventListener('resize', sync_sidebar_during_header_animation);

            if(header) {
                var header_observer = new MutationObserver(sync_sidebar_during_header_animation);

                header_observer.observe(header, {
                    attributes: true,
                    attributeFilter: ['style', 'class']
                });

                header.addEventListener('transitionrun', sync_sidebar_during_header_animation);
                header.addEventListener('transitionend', sync_blog_sidebar);
            }

            sync_blog_sidebar();
        },

        password_visibility: function() {
            document.querySelectorAll('[data-password-toggle]').forEach(function(button) {
                var container = button.parentElement;
                var input = container ? container.querySelector('[data-password-input]') : null;

                if(!input) {
                    return;
                }

                button.addEventListener('click', function() {
                    var is_visible = input.type === 'text';
                    input.type = is_visible ? 'password' : 'text';
                    button.setAttribute('aria-label', is_visible ? button.getAttribute('data-show-label') : button.getAttribute('data-hide-label'));
                });
            });
        },

        initialize: function() {
            wordpress_demo_theme.theme_mode();
            wordpress_demo_theme.desktop_dropdown();
            wordpress_demo_theme.header_scroll();
            wordpress_demo_theme.mobile_menu();
            wordpress_demo_theme.mobile_accordion();
            wordpress_demo_theme.back_to_top();
            wordpress_demo_theme.search_modal();
            wordpress_demo_theme.post_share();
            wordpress_demo_theme.post_interactions();
            wordpress_demo_theme.comment_actions();
            wordpress_demo_theme.table_of_contents();
            wordpress_demo_theme.single_post_sidebar();
            wordpress_demo_theme.password_visibility();
        }
    };

    var initialize_app = function() {
        if(!document.body) {
            return;
        }

        if(!document.body.getAttribute('data-theme')) {
            document.body.setAttribute('data-theme', get_initial_theme());
        }

        wordpress_demo_theme.initialize();
    };

    if(document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize_app);
    }
    else {
        initialize_app();
    }
})();
