/**
 * Bootstrap theme-related localStorage from DB.
 * Expects window.__themeSettingsFromDb to be set by the layout (from $themeSettings['settings']).
 * Run before main.js / new-switcher.js so layout applies correctly.
 */
(function () {
    try {
        var s = window.__themeSettingsFromDb;
        if (!s || typeof s !== 'object') return;

        // Colors
        if (s.primary_color) {
            localStorage.setItem('primaryRGB', s.primary_color);
        } else {
            localStorage.removeItem('primaryRGB');
        }
        if (s.background_color && s.background_light_color) {
            localStorage.setItem('bodyBgRGB', s.background_color);
            localStorage.setItem('bodylightRGB', s.background_light_color);
        } else {
            localStorage.removeItem('bodyBgRGB');
            localStorage.removeItem('bodylightRGB');
        }

        // Layout (only horizontal stored; vertical = no key)
        if (s.layout === 'horizontal') {
            localStorage.setItem('zenolayout', 'horizontal');
        } else {
            localStorage.removeItem('zenolayout');
        }

        // Width
        localStorage.removeItem('zenofullwidth');
        localStorage.removeItem('zenoboxed');
        if (s.width === 'boxed') {
            localStorage.setItem('zenoboxed', 'true');
        } else if (s.width === 'fullwidth') {
            localStorage.setItem('zenofullwidth', 'true');
        }

        // Page style
        localStorage.removeItem('zenoregular');
        localStorage.removeItem('zenoclassic');
        localStorage.removeItem('zenomodern');
        if (s.page_style === 'classic') {
            localStorage.setItem('zenoclassic', 'true');
        } else if (s.page_style === 'modern') {
            localStorage.setItem('zenomodern', 'true');
        } else if (s.page_style === 'regular') {
            localStorage.setItem('zenoregular', 'true');
        }

        // Header position
        localStorage.removeItem('zenoheaderfixed');
        localStorage.removeItem('zenoheaderscrollable');
        if (s.header_position === 'fixed') {
            localStorage.setItem('zenoheaderfixed', 'true');
        } else if (s.header_position === 'scrollable') {
            localStorage.setItem('zenoheaderscrollable', 'true');
        }

        // Menu position
        localStorage.removeItem('zenomenufixed');
        localStorage.removeItem('zenomenuscrollable');
        if (s.menu_position === 'fixed') {
            localStorage.setItem('zenomenufixed', 'true');
        } else if (s.menu_position === 'scrollable') {
            localStorage.setItem('zenomenuscrollable', 'true');
        }

        // Vertical/menu behavior
        localStorage.removeItem('zenoverticalstyles');
        localStorage.removeItem('zenonavstyles');
        if (s.menu_behavior) {
            var v = s.menu_behavior;
            if (['default', 'closed', 'detached', 'icontext', 'overlay', 'doublemenu'].indexOf(v) !== -1) {
                localStorage.setItem('zenoverticalstyles', v);
            } else if (['menu-click', 'menu-hover', 'icon-click', 'icon-hover'].indexOf(v) !== -1) {
                localStorage.setItem('zenonavstyles', v);
            }
        }

        // Header/menu styles
        if (localStorage.zenodarktheme) {
            localStorage.setItem('zenoHeader', 'transparent');
            localStorage.setItem('zenoMenu', 'dark');
        }

        // Loader
        if (s.loader === 'enable') {
            localStorage.setItem('loaderEnable', 'true');
        } else {
            localStorage.removeItem('loaderEnable');
        }
    } catch (e) {
        if (window.console && console.warn) {
            console.warn('Theme localStorage bootstrap failed:', e);
        }
    }
})();
