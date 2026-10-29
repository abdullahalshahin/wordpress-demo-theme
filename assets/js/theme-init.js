(function () {
    'use strict';

    var theme_storage_key = 'wordpress-demo-theme';
    var theme_light = 'light';
    var theme_dark = 'dark';
    var system_theme = window.matchMedia
        ? window.matchMedia('(prefers-color-scheme: dark)')
        : null;

    var get_system_theme = function () {
        return system_theme && system_theme.matches ? theme_dark : theme_light;
    };

    var get_saved_theme = function () {
        try {
            var saved_theme = localStorage.getItem(theme_storage_key);

            if (saved_theme === theme_light || saved_theme === theme_dark) {
                return saved_theme;
            }
        }
        catch (error) {
            return null;
        }

        return null;
    };

    var initial_theme = get_saved_theme() || get_system_theme();

    var apply_initial_theme = function () {
        if (!document.body) {
            window.requestAnimationFrame(apply_initial_theme);
            return;
        }

        document.body.setAttribute('data-theme', initial_theme);
    };

    apply_initial_theme();
})();
