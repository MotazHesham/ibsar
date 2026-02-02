"use strict";

// Set to true to persist theme settings in localStorage; false = DB only (no theme keys in localStorage)
const THEME_SAVE_TO_LOCAL_STORAGE = false;
const themeSetItem = (key, value, saveToLocalStorage = THEME_SAVE_TO_LOCAL_STORAGE) => { if (saveToLocalStorage) localStorage.setItem(key, value); };

import {
    ResizeMenu,
    toggleSidemenu,
    closedSidemenuFn,
    detachedFn,
    iconTextFn,
    doubletFn,
    menuClickFn,
    menuhoverFn,
    iconClickFn,
    iconHoverFn,
    setNavActive,
    clearNavDropdown,
    checkHoriMenu,
    iconOverayFn
} from '/assets/js/defaultmenu.js';

let mainContent;

// Theme API URLs (injected by layout); direction and theme_mode are localStorage-only
const getThemeUpdateUrl = () => (typeof window !== 'undefined' && window.THEME_UPDATE_URL) || '/admin/settings/update-theme';
const getThemeGetSettingsUrl = () => (typeof window !== 'undefined' && window.THEME_GET_SETTINGS_URL) || '/admin/settings/get-theme-settings';

function updateThemeSetting(key, value) { 
    const formData = new FormData();
    formData.append(key, value);
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) formData.append('_token', token);
    fetch(getThemeUpdateUrl(), {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': token || '' }
    }).then(r => r.json()).catch(() => {});
}

function updateThemeSettingsMulti(data) {
    const formData = new FormData();
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) formData.append('_token', token);
    Object.keys(data).forEach(k => formData.append(k, data[k]));
    fetch(getThemeUpdateUrl(), {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': token || '' }
    }).then(r => r.json()).catch(() => {});
}

function loadThemeSettingsFromDatabase() {
    return fetch(getThemeGetSettingsUrl(), { method: 'GET', credentials: 'same-origin' })
        .then(r => r.json())
        .then(data => data.success ? data.settings : {})
        .catch(() => ({}));
}

const PRESET_PRIMARY = ['64, 100, 221', '207, 117, 225', '199, 89, 106', '1, 159, 162', '139, 149, 4'];
const PRESET_BACKGROUND = ['34, 49, 153', '147, 52, 150', '135, 44, 47', '3, 81, 60', '73, 78, 1'];

function ensurePrimaryDynamicOption(value) {
    const wrap = document.getElementById('switcher-primary-dynamic-wrap');
    if (!wrap) return;
    wrap.innerHTML = '';
    wrap.classList.add('d-none');
    if (!value || PRESET_PRIMARY.includes(value)) return;
    document.querySelectorAll('input[name="theme-primary"]').forEach(el => { el.checked = false; });
    const html = document.querySelector('html');
    const div = document.createElement('div');
    div.className = 'form-check switch-select me-3';
    const label = document.createElement('label');
    label.className = 'form-check-label d-flex align-items-center';
    const input = document.createElement('input');
    input.type = 'radio';
    input.className = 'form-check-input color-input';
    input.name = 'theme-primary';
    input.id = 'switcher-primary-custom';
    input.dataset.value = value;
    input.title = 'Custom';
    input.addEventListener('change', function () {
        if (this.checked) {
            html.style.setProperty('--primary-rgb', value);
            themeSetItem('primaryRGB', value);
            updateThemeSetting('primary_color', value);
        }
    });
    const swatch = document.createElement('span');
    swatch.className = 'rounded ms-1';
    swatch.style.cssText = 'width:1.25rem;height:1.25rem;background:rgb(' + value + ');border:1px solid rgba(0,0,0,.1)';
    const text = document.createElement('span');
    text.className = 'ms-1 small text-muted';
    text.textContent = 'Custom';
    label.appendChild(input);
    label.appendChild(swatch);
    label.appendChild(text);
    div.appendChild(label);
    wrap.appendChild(div);
    wrap.classList.remove('d-none');
    input.checked = true;
}

function ensureBackgroundDynamicOption(bgValue, lightValue) {
    const wrap = document.getElementById('switcher-background-dynamic-wrap');
    if (!wrap) return;
    wrap.innerHTML = '';
    wrap.classList.add('d-none');
    if (!bgValue || !lightValue || PRESET_BACKGROUND.includes(bgValue)) return;
    document.querySelectorAll('input[name="theme-background"]').forEach(el => { el.checked = false; });
    const html = document.querySelector('html');
    const div = document.createElement('div');
    div.className = 'form-check switch-select me-3';
    const label = document.createElement('label');
    label.className = 'form-check-label d-flex align-items-center';
    const input = document.createElement('input');
    input.type = 'radio';
    input.className = 'form-check-input color-input';
    input.name = 'theme-background';
    input.id = 'switcher-background-custom';
    input.dataset.bg = bgValue;
    input.dataset.light = lightValue;
    input.title = 'Custom';
    input.addEventListener('change', function () {
        if (this.checked) {
            html.setAttribute('data-theme-mode', 'dark');
            html.setAttribute('data-menu-styles', 'dark');
            html.setAttribute('data-header-styles', 'dark');
            html.style.setProperty('--body-bg-rgb', this.dataset.bg);
            html.style.setProperty('--body-bg-rgb2', this.dataset.light);
            html.style.setProperty('--light-rgb', this.dataset.light);
            html.style.setProperty('--form-control-bg', 'rgb(' + this.dataset.light + ')');
            html.style.setProperty('--input-border', 'rgba(255,255,255,0.1)');
            html.style.setProperty('--gray-3', 'rgba(255,255,255,0.1)');
            themeSetItem('bodyBgRGB', this.dataset.bg);
            themeSetItem('bodylightRGB', this.dataset.light);
            updateThemeSetting('background_color', this.dataset.bg);
            updateThemeSetting('background_light_color', this.dataset.light);
        }
    });
    const swatch = document.createElement('span');
    swatch.className = 'rounded ms-1';
    swatch.style.cssText = 'width:1.25rem;height:1.25rem;background:rgb(' + bgValue + ');border:1px solid rgba(0,0,0,.1)';
    const text = document.createElement('span');
    text.className = 'ms-1 small text-muted';
    text.textContent = 'Custom';
    label.appendChild(input);
    label.appendChild(swatch);
    label.appendChild(text);
    div.appendChild(label);
    wrap.appendChild(div);
    wrap.classList.remove('d-none');
    input.checked = true;
}

function applyThemeSettingsFromDatabase(settings, source) {
    const html = document.querySelector('html');
    if (!settings || typeof settings !== 'object') return;
    if (settings.layout === 'horizontal') horizontalClickFn(); else if (settings.layout) verticalFn();
    if (settings.width) html.setAttribute('data-width', settings.width);
    if (!localStorage.zenodarktheme) { 
        if (settings.header_style) html.setAttribute('data-header-styles', settings.header_style);
        if (settings.menu_style) html.setAttribute('data-menu-styles', settings.menu_style);
    }
    if (settings.page_style) html.setAttribute('data-page-style', settings.page_style);
    if (settings.header_position) html.setAttribute('data-header-position', settings.header_position);
    if (settings.menu_position) html.setAttribute('data-menu-position', settings.menu_position);
    if (settings.menu_behavior) {
        const v = settings.menu_behavior;
        if (['default', 'closed', 'detached', 'icontext', 'overlay', 'doublemenu'].includes(v)) {
            html.setAttribute('data-vertical-style', v);
            if (v === 'closed') closedSidemenuFn();
            else if (v === 'detached') detachedFn();
            else if (v === 'icontext') iconTextFn();
            else if (v === 'overlay') iconOverayFn();
            else if (v === 'doublemenu') doubletFn();
        } else if (['menu-click', 'menu-hover', 'icon-click', 'icon-hover'].includes(v)) {
            html.removeAttribute('data-vertical-style');
            html.setAttribute('data-nav-style', v);
            if (v === 'menu-click') menuClickFn();
            else if (v === 'menu-hover') menuhoverFn();
            else if (v === 'icon-click') iconClickFn();
            else if (v === 'icon-hover') iconHoverFn();
        }
    }
    if (settings.loader) html.setAttribute('loader', settings.loader);
    if (settings.primary_color) html.style.setProperty('--primary-rgb', settings.primary_color);
    if (settings.background_color) html.style.setProperty('--body-bg-rgb', settings.background_color);
    if (settings.background_light_color) {
        html.style.setProperty('--body-bg-rgb2', settings.background_light_color);
        html.style.setProperty('--light-rgb', settings.background_light_color);
        html.style.setProperty('--form-control-bg', 'rgb(' + settings.background_light_color + ')');
        html.style.setProperty('--input-border', 'rgba(255,255,255,0.1)');
        html.style.setProperty('--gray-3', 'rgba(255,255,255,0.1)');
    }
    // Sync switcher radios from DB (so panel reflects DB state)
    const q = (id) => document.querySelector(id);
    if (settings.layout === 'horizontal' && q('#switcher-horizontal')) q('#switcher-horizontal').checked = true;
    else if (q('#switcher-vertical')) q('#switcher-vertical').checked = true;
    if (settings.width === 'boxed' && q('#switcher-boxed')) q('#switcher-boxed').checked = true;
    else if (q('#switcher-full-width')) q('#switcher-full-width').checked = true;
    const headerIds = { light: '#switcher-header-light', dark: '#switcher-header-dark', color: '#switcher-header-primary', gradient: '#switcher-header-gradient', transparent: '#switcher-header-transparent' };
    if (settings.header_style && headerIds[settings.header_style] && q(headerIds[settings.header_style])) q(headerIds[settings.header_style]).checked = true;
    const menuIds = { light: '#switcher-menu-light', dark: '#switcher-menu-dark', color: '#switcher-menu-primary', gradient: '#switcher-menu-gradient', transparent: '#switcher-menu-transparent' };
    if (settings.menu_style && menuIds[settings.menu_style] && q(menuIds[settings.menu_style])) q(menuIds[settings.menu_style]).checked = true;
    if (settings.page_style === 'classic' && q('#switcher-classic')) q('#switcher-classic').checked = true;
    else if (settings.page_style === 'modern' && q('#switcher-modern')) q('#switcher-modern').checked = true;
    else if (q('#switcher-regular')) q('#switcher-regular').checked = true;
    if (settings.header_position === 'scrollable' && q('#switcher-header-scroll')) q('#switcher-header-scroll').checked = true;
    else if (q('#switcher-header-fixed')) q('#switcher-header-fixed').checked = true;
    if (settings.menu_position === 'scrollable' && q('#switcher-menu-scroll')) q('#switcher-menu-scroll').checked = true;
    else if (q('#switcher-menu-fixed')) q('#switcher-menu-fixed').checked = true;
    if (settings.menu_behavior) {
        const mid = { 'default': '#switcher-default-menu', 'closed': '#switcher-closed-menu', 'icontext': '#switcher-icontext-menu', 'overlay': '#switcher-icon-overlay', 'detached': '#switcher-detached', 'doublemenu': '#switcher-double-menu', 'menu-click': '#switcher-menu-click', 'menu-hover': '#switcher-menu-hover', 'icon-click': '#switcher-icon-click', 'icon-hover': '#switcher-icon-hover' }[settings.menu_behavior];
        if (mid && q(mid)) q(mid).checked = true;
    }
    if (settings.loader === 'enable' && q('#switcher-loader-enable')) q('#switcher-loader-enable').checked = true;
    else if (q('#switcher-loader-disable')) q('#switcher-loader-disable').checked = true;
    if (settings.primary_color) {
        if (PRESET_PRIMARY.includes(settings.primary_color)) {
            const prim = { '64, 100, 221': '#switcher-primary', '207, 117, 225': '#switcher-primary1', '199, 89, 106': '#switcher-primary2', '1, 159, 162': '#switcher-primary3', '139, 149, 4': '#switcher-primary4' }[settings.primary_color];
            if (prim && q(prim)) q(prim).checked = true;
        } else {
            ensurePrimaryDynamicOption(settings.primary_color);
        }
    }
    if (settings.background_color) {
        if (PRESET_BACKGROUND.includes(settings.background_color)) {
            const bg = { '34, 49, 153': '#switcher-background', '147, 52, 150': '#switcher-background1', '135, 44, 47': '#switcher-background2', '3, 81, 60': '#switcher-background3', '73, 78, 1': '#switcher-background4' }[settings.background_color];
            if (bg && q(bg)) q(bg).checked = true;
        } else {
            ensureBackgroundDynamicOption(settings.background_color, settings.background_light_color || settings.background_color);
        }
    }
}

// Allow picker (custom.js) to save to DB and show dynamic option via custom events
document.addEventListener('theme-primary-changed', function (e) {
    if (e.detail && e.detail.value) {
        updateThemeSetting('primary_color', e.detail.value);
        ensurePrimaryDynamicOption(e.detail.value);
    }
});
document.addEventListener('theme-background-changed', function (e) {
    if (e.detail && e.detail.bg && e.detail.light) {
        updateThemeSetting('background_color', e.detail.bg);
        updateThemeSetting('background_light_color', e.detail.light);
        ensureBackgroundDynamicOption(e.detail.bg, e.detail.light);
    }
});

(function () {
    let html = document.querySelector('html');
    mainContent = document.querySelector('.main-content');
    if (document.querySelector("#switcher-canvas")) {
        // 1) Apply DB theme first (from server so no flash)
        const initial = (typeof window !== 'undefined' && window.__INITIAL_THEME_SETTINGS__) || null;
        if (initial && typeof initial === 'object' && Object.keys(initial).length) {
            applyThemeSettingsFromDatabase(initial, 'server');
        }
        // 2) Then only theme mode (light/dark) from localStorage — never overwrite DB options
        localStorageBackup2();
        switcherClick();
        checkOptions();
        // 3) Sync from API in background
        loadThemeSettingsFromDatabase().then(settings => {
            if (settings && typeof settings === 'object' && Object.keys(settings).length) {
                applyThemeSettingsFromDatabase(settings, 'API');
            }
            checkOptions();
            setTimeout(() => checkOptions(), 1000);
        }).catch(() => {
            setTimeout(() => checkOptions(), 1000);
        });
    }
})();

function switcherClick() {
    let verticalBtn, horiBtn, lightBtn, darkBtn, boxedBtn, fullwidthBtn, scrollHeaderBtn, scrollMenuBtn, fixedHeaderBtn, fixedMenuBtn, lightHeaderBtn, darkHeaderBtn, colorHeaderBtn, gradientHeaderBtn, lightMenuBtn, darkMenuBtn, colorMenuBtn, gradientMenuBtn, transparentMenuBtn, transparentHeaderBtn, regular, classic, modern, defaultBtn, closedBtn, iconTextBtn, detachedBtn, overlayBtn, doubleBtn, menuClickBtn, menuHoverBtn, iconClickBtn, iconHoverBtn, primaryDefaultColor1Btn, primaryDefaultColor2Btn, primaryDefaultColor3Btn, primaryDefaultColor4Btn, primaryDefaultColor5Btn, bgDefaultColor1Btn, bgDefaultColor2Btn, bgDefaultColor3Btn, bgDefaultColor4Btn, bgDefaultColor5Btn, ResetAll, resetBtn, loaderEnable, loaderDisable;
    let html = document.querySelector('html');
    lightBtn = document.querySelector('#switcher-light-theme');
    darkBtn = document.querySelector('#switcher-dark-theme'); 
    verticalBtn = document.querySelector('#switcher-vertical');
    horiBtn = document.querySelector('#switcher-horizontal');
    boxedBtn = document.querySelector('#switcher-boxed');
    fullwidthBtn = document.querySelector('#switcher-full-width');
    fixedMenuBtn = document.querySelector('#switcher-menu-fixed');
    scrollMenuBtn = document.querySelector('#switcher-menu-scroll');
    fixedHeaderBtn = document.querySelector('#switcher-header-fixed');
    scrollHeaderBtn = document.querySelector('#switcher-header-scroll');
    lightHeaderBtn = document.querySelector('#switcher-header-light');
    darkHeaderBtn = document.querySelector('#switcher-header-dark');
    colorHeaderBtn = document.querySelector('#switcher-header-primary');
    gradientHeaderBtn = document.querySelector('#switcher-header-gradient');
    transparentHeaderBtn = document.querySelector('#switcher-header-transparent');
    lightMenuBtn = document.querySelector('#switcher-menu-light');
    darkMenuBtn = document.querySelector('#switcher-menu-dark');
    colorMenuBtn = document.querySelector('#switcher-menu-primary');
    gradientMenuBtn = document.querySelector('#switcher-menu-gradient');
    transparentMenuBtn = document.querySelector('#switcher-menu-transparent');
    regular = document.querySelector('#switcher-regular');
    classic = document.querySelector('#switcher-classic');
    modern = document.querySelector('#switcher-modern');
    defaultBtn = document.querySelector('#switcher-default-menu');
    menuClickBtn = document.querySelector('#switcher-menu-click');
    menuHoverBtn = document.querySelector('#switcher-menu-hover');
    iconClickBtn = document.querySelector('#switcher-icon-click');
    iconHoverBtn = document.querySelector('#switcher-icon-hover');
    closedBtn = document.querySelector('#switcher-closed-menu');
    iconTextBtn = document.querySelector('#switcher-icontext-menu');
    overlayBtn = document.querySelector('#switcher-icon-overlay');
    doubleBtn = document.querySelector('#switcher-double-menu');
    detachedBtn = document.querySelector('#switcher-detached');
    resetBtn = document.querySelector('#resetbtn');
    primaryDefaultColor1Btn = document.querySelector('#switcher-primary');
    primaryDefaultColor2Btn = document.querySelector('#switcher-primary1');
    primaryDefaultColor3Btn = document.querySelector('#switcher-primary2');
    primaryDefaultColor4Btn = document.querySelector('#switcher-primary3');
    primaryDefaultColor5Btn = document.querySelector('#switcher-primary4');
    bgDefaultColor1Btn = document.querySelector('#switcher-background');
    bgDefaultColor2Btn = document.querySelector('#switcher-background1');
    bgDefaultColor3Btn = document.querySelector('#switcher-background2');
    bgDefaultColor4Btn = document.querySelector('#switcher-background3');
    bgDefaultColor5Btn = document.querySelector('#switcher-background4'); 
    ResetAll = document.querySelector('#reset-all');
    loaderEnable = document.querySelector('#switcher-loader-enable');
    loaderDisable = document.querySelector('#switcher-loader-disable');

    // primary theme
    if (primaryDefaultColor1Btn) primaryDefaultColor1Btn.addEventListener('click', () => {
        themeSetItem("primaryRGB", "64, 100, 221");
        html.style.setProperty('--primary-rgb', `64, 100, 221`);
        updateThemeSetting('primary_color', '64, 100, 221');
    });
    if (primaryDefaultColor2Btn) primaryDefaultColor2Btn.addEventListener('click', () => {
        themeSetItem("primaryRGB", "207, 117, 225");
        html.style.setProperty('--primary-rgb', `207, 117, 225`);
        updateThemeSetting('primary_color', '207, 117, 225');
    });
    if (primaryDefaultColor3Btn) primaryDefaultColor3Btn.addEventListener('click', () => {
        themeSetItem("primaryRGB", "199, 89, 106");
        html.style.setProperty('--primary-rgb', `199, 89, 106`);
        updateThemeSetting('primary_color', '199, 89, 106');
    });
    if (primaryDefaultColor4Btn) primaryDefaultColor4Btn.addEventListener('click', () => {
        themeSetItem("primaryRGB", "1, 159, 162");
        html.style.setProperty('--primary-rgb', `1, 159, 162`);
        updateThemeSetting('primary_color', '1, 159, 162');
    });
    if (primaryDefaultColor5Btn) primaryDefaultColor5Btn.addEventListener('click', () => {
        themeSetItem("primaryRGB", "139, 149, 4");
        html.style.setProperty('--primary-rgb', `139, 149, 4`);
        updateThemeSetting('primary_color', '139, 149, 4');
    });

    // Background theme (lighting stays localStorage; save colors to DB)
    if (bgDefaultColor1Btn) bgDefaultColor1Btn.addEventListener('click', () => {
        themeSetItem('bodyBgRGB', "34, 49, 153");
        themeSetItem('bodylightRGB', "48, 63, 167");
        html.setAttribute('data-theme-mode', 'dark');
        html.setAttribute('data-menu-styles', 'dark');
        html.setAttribute('data-header-styles', 'dark');
        document.querySelector('html').style.setProperty('--body-bg-rgb', localStorage.bodyBgRGB);
        document.querySelector('html').style.setProperty('--body-bg-rgb2', localStorage.bodylightRGB);
        document.querySelector('html').style.setProperty('--light-rgb', "48, 63, 167");
        document.querySelector('html').style.setProperty('--form-control-bg', "rgb(48, 63, 167)");
        document.querySelector('html').style.setProperty('--input-border', "rgba(255,255,255,0.1)");
        document.querySelector('html').style.setProperty('--gray-3', "rgba(255,255,255,0.1)");
        document.querySelector('#switcher-dark-theme').checked = true;
        document.querySelector('#switcher-menu-dark').checked = true;
        document.querySelector('#switcher-header-dark').checked = true;
        themeSetItem("zenoMenu", "dark");
        themeSetItem("zenoHeader", "dark");
        updateThemeSetting('background_color', '34, 49, 153');
        updateThemeSetting('background_light_color', '48, 63, 167');
        updateThemeSetting('menu_style', 'dark');
        updateThemeSetting('header_style', 'dark');
    });
    if (bgDefaultColor2Btn) bgDefaultColor2Btn.addEventListener('click', () => {
        themeSetItem('bodyBgRGB', "147, 52, 150");
        themeSetItem('bodylightRGB', "161, 66, 164");
        html.setAttribute('data-theme-mode', 'dark');
        html.setAttribute('data-menu-styles', 'dark');
        html.setAttribute('data-header-styles', 'dark');
        document.querySelector('html').style.setProperty('--body-bg-rgb', localStorage.bodyBgRGB);
        document.querySelector('html').style.setProperty('--body-bg-rgb2', localStorage.bodylightRGB);
        document.querySelector('html').style.setProperty('--light-rgb', "161, 66, 164");
        document.querySelector('html').style.setProperty('--form-control-bg', "rgb(161, 66, 164)");
        document.querySelector('html').style.setProperty('--input-border', "rgba(255,255,255,0.1)");
        document.querySelector('html').style.setProperty('--gray-3', "rgba(255,255,255,0.1)");
        document.querySelector('#switcher-dark-theme').checked = true;
        document.querySelector('#switcher-menu-dark').checked = true;
        document.querySelector('#switcher-header-dark').checked = true;
        themeSetItem("zenoMenu", "dark");
        themeSetItem("zenoHeader", "dark");
        updateThemeSetting('background_color', '147, 52, 150');
        updateThemeSetting('background_light_color', '161, 66, 164');
        updateThemeSetting('menu_style', 'dark');
        updateThemeSetting('header_style', 'dark');
    });
    if (bgDefaultColor3Btn) bgDefaultColor3Btn.addEventListener('click', () => {
        themeSetItem('bodyBgRGB', "135, 44, 47");
        themeSetItem('bodylightRGB', "149, 58, 61");
        html.setAttribute('data-theme-mode', 'dark');
        html.setAttribute('data-menu-styles', 'dark');
        html.setAttribute('data-header-styles', 'dark');
        document.querySelector('html').style.setProperty('--body-bg-rgb', localStorage.bodyBgRGB);
        document.querySelector('html').style.setProperty('--body-bg-rgb2', localStorage.bodylightRGB);
        document.querySelector('html').style.setProperty('--light-rgb', "149, 58, 61");
        document.querySelector('html').style.setProperty('--form-control-bg', "rgb(149, 58, 61)");
        document.querySelector('html').style.setProperty('--input-border', "rgba(255,255,255,0.1)");
        document.querySelector('html').style.setProperty('--gray-3', "rgba(255,255,255,0.1)");
        document.querySelector('#switcher-dark-theme').checked = true;
        document.querySelector('#switcher-menu-dark').checked = true;
        document.querySelector('#switcher-header-dark').checked = true;
        themeSetItem("zenoMenu", "dark");
        themeSetItem("zenoHeader", "dark");
        updateThemeSetting('background_color', '135, 44, 47');
        updateThemeSetting('background_light_color', '149, 58, 61');
        updateThemeSetting('menu_style', 'dark');
        updateThemeSetting('header_style', 'dark');
    });
    if (bgDefaultColor4Btn) bgDefaultColor4Btn.addEventListener('click', () => {
        themeSetItem('bodyBgRGB', "3, 81, 60");
        themeSetItem('bodylightRGB', "8, 99, 75");
        html.setAttribute('data-theme-mode', 'dark');
        html.setAttribute('data-menu-styles', 'dark');
        html.setAttribute('data-header-styles', 'dark');
        document.querySelector('html').style.setProperty('--body-bg-rgb', localStorage.bodyBgRGB);
        document.querySelector('html').style.setProperty('--body-bg-rgb2', localStorage.bodylightRGB);
        document.querySelector('html').style.setProperty('--light-rgb', "8, 99, 75");
        document.querySelector('html').style.setProperty('--form-control-bg', "rgb(8, 99, 75)");
        document.querySelector('html').style.setProperty('--input-border', "rgba(255,255,255,0.1)");
        document.querySelector('html').style.setProperty('--gray-3', "rgba(255,255,255,0.1)");
        document.querySelector('#switcher-dark-theme').checked = true;
        document.querySelector('#switcher-menu-dark').checked = true;
        document.querySelector('#switcher-header-dark').checked = true;
        themeSetItem("zenoMenu", "dark");
        themeSetItem("zenoHeader", "dark");
        updateThemeSetting('background_color', '3, 81, 60');
        updateThemeSetting('background_light_color', '8, 99, 75');
        updateThemeSetting('menu_style', 'dark');
        updateThemeSetting('header_style', 'dark');
    });
    if (bgDefaultColor5Btn) bgDefaultColor5Btn.addEventListener('click', () => {
        themeSetItem('bodyBgRGB', "73, 78, 1");
        themeSetItem('bodylightRGB', "84, 89, 4");
        html.setAttribute('data-theme-mode', 'dark');
        html.setAttribute('data-menu-styles', 'dark');
        html.setAttribute('data-header-styles', 'dark');
        document.querySelector('html').style.setProperty('--body-bg-rgb', localStorage.bodyBgRGB);
        document.querySelector('html').style.setProperty('--body-bg-rgb2', localStorage.bodylightRGB);
        document.querySelector('html').style.setProperty('--light-rgb', "84, 89, 4");
        document.querySelector('html').style.setProperty('--form-control-bg', "rgb(84, 89, 4)");
        document.querySelector('html').style.setProperty('--input-border', "rgba(255,255,255,0.1)");
        document.querySelector('html').style.setProperty('--gray-3', "rgba(255,255,255,0.1)");
        document.querySelector('#switcher-dark-theme').checked = true;
        document.querySelector('#switcher-menu-dark').checked = true;
        document.querySelector('#switcher-header-dark').checked = true;
        themeSetItem("zenoMenu", "dark");
        themeSetItem("zenoHeader", "dark");
        updateThemeSetting('background_color', '73, 78, 1');
        updateThemeSetting('background_light_color', '84, 89, 4');
        updateThemeSetting('menu_style', 'dark');
        updateThemeSetting('header_style', 'dark');
    }); 

    /* Light Layout Start */
    let lightThemeVar = lightBtn.addEventListener('click', () => {
        lightFn();
        themeSetItem("zenoHeader", 'light', true);
        localStorage.removeItem("bodylightRGB")
        localStorage.removeItem("bodyBgRGB")
        localStorage.removeItem("zenoMenu")
        if (html.getAttribute('data-nav-layout') === 'horizontal') {
            html.setAttribute('data-header-styles', 'light');
        }
    })
    /* Light Layout End */

    /* Dark Layout Start */
    let darkThemeVar = darkBtn.addEventListener('click', () => {
        darkFn();
        themeSetItem("zenoMenu", 'dark', true);
        themeSetItem("zenoHeader", 'transparent', true);
        if (html.getAttribute('data-nav-layout') === 'horizontal') {
            html.setAttribute('data-header-styles', 'dark');
        }
    });
    /* Dark Layout End */

    /* Light Menu Start */
    if (lightMenuBtn) lightMenuBtn.addEventListener('click', () => {
        html.setAttribute('data-menu-styles', 'light');
        themeSetItem("zenoMenu", 'light');
        updateThemeSetting('menu_style', 'light');
    });
    if (colorMenuBtn) colorMenuBtn.addEventListener('click', () => {
        html.setAttribute('data-menu-styles', 'color');
        themeSetItem("zenoMenu", 'color');
        updateThemeSetting('menu_style', 'color');
    });
    if (darkMenuBtn) darkMenuBtn.addEventListener('click', () => {
        html.setAttribute('data-menu-styles', 'dark');
        themeSetItem("zenoMenu", 'dark');
        updateThemeSetting('menu_style', 'dark');
    });
    if (gradientMenuBtn) gradientMenuBtn.addEventListener('click', () => {
        html.setAttribute('data-menu-styles', 'gradient');
        themeSetItem("zenoMenu", 'gradient');
        updateThemeSetting('menu_style', 'gradient');
    });
    if (transparentMenuBtn) transparentMenuBtn.addEventListener('click', () => {
        html.setAttribute('data-menu-styles', 'transparent');
        themeSetItem("zenoMenu", 'transparent');
        updateThemeSetting('menu_style', 'transparent');
    });

    if (lightHeaderBtn) lightHeaderBtn.addEventListener('click', () => {
        html.setAttribute('data-header-styles', 'light');
        themeSetItem("zenoHeader", 'light');
        updateThemeSetting('header_style', 'light');
    });
    if (colorHeaderBtn) colorHeaderBtn.addEventListener('click', () => {
        html.setAttribute('data-header-styles', 'color');
        themeSetItem("zenoHeader", 'color');
        updateThemeSetting('header_style', 'color');
    });
    if (darkHeaderBtn) darkHeaderBtn.addEventListener('click', () => {
        html.setAttribute('data-header-styles', 'dark');
        themeSetItem("zenoHeader", 'dark');
        updateThemeSetting('header_style', 'dark');
    });
    if (gradientHeaderBtn) gradientHeaderBtn.addEventListener('click', () => {
        html.setAttribute('data-header-styles', 'gradient');
        themeSetItem("zenoHeader", 'gradient');
        updateThemeSetting('header_style', 'gradient');
    });
    if (transparentHeaderBtn) transparentHeaderBtn.addEventListener('click', () => {
        html.setAttribute('data-header-styles', 'transparent');
        themeSetItem("zenoHeader", 'transparent');
        updateThemeSetting('header_style', 'transparent');
    });

    if (fullwidthBtn) fullwidthBtn.addEventListener('click', () => {
        html.setAttribute('data-width', 'fullwidth');
        themeSetItem("zenofullwidth", true);
        localStorage.removeItem("zenoboxed");
        updateThemeSetting('width', 'fullwidth');
    });
    if (boxedBtn) boxedBtn.addEventListener('click', () => {
        html.setAttribute('data-width', 'boxed');
        themeSetItem("zenoboxed", true);
        localStorage.removeItem("zenofullwidth");
        checkHoriMenu();
        updateThemeSetting('width', 'boxed');
    });

    if (regular) regular.addEventListener('click', () => {
        html.setAttribute('data-page-style', 'regular');
        themeSetItem("zenoregular", true);
        localStorage.removeItem("zenoclassic");
        localStorage.removeItem("zenomodern");
        updateThemeSetting('page_style', 'regular');
    });
    if (classic) classic.addEventListener('click', () => {
        html.setAttribute('data-page-style', 'classic');
        themeSetItem("zenoclassic", true);
        localStorage.removeItem("zenoregular");
        localStorage.removeItem("zenomodern");
        updateThemeSetting('page_style', 'classic');
    });
    if (modern) modern.addEventListener('click', () => {
        html.setAttribute('data-page-style', 'modern');
        themeSetItem("zenomodern", true);
        localStorage.removeItem("zenoregular");
        localStorage.removeItem("zenoclassic");
        updateThemeSetting('page_style', 'modern');
    });

    if (fixedHeaderBtn) fixedHeaderBtn.addEventListener('click', () => {
        html.setAttribute('data-header-position', 'fixed');
        themeSetItem("zenoheaderfixed", true);
        localStorage.removeItem("zenoheaderscrollable");
        updateThemeSetting('header_position', 'fixed');
    });
    if (scrollHeaderBtn) scrollHeaderBtn.addEventListener('click', () => {
        html.setAttribute('data-header-position', 'scrollable');
        themeSetItem("zenoheaderscrollable", true);
        localStorage.removeItem("zenoheaderfixed");
        updateThemeSetting('header_position', 'scrollable');
    });
    if (fixedMenuBtn) fixedMenuBtn.addEventListener('click', () => {
        html.setAttribute('data-menu-position', 'fixed');
        themeSetItem("zenomenufixed", true);
        localStorage.removeItem("zenomenuscrollable");
        updateThemeSetting('menu_position', 'fixed');
    });
    if (scrollMenuBtn) scrollMenuBtn.addEventListener('click', () => {
        html.setAttribute('data-menu-position', 'scrollable');
        themeSetItem("zenomenuscrollable", true);
        localStorage.removeItem("zenomenufixed");
        updateThemeSetting('menu_position', 'scrollable');
    });

    if (defaultBtn) defaultBtn.addEventListener('click', () => {
        html.setAttribute('data-vertical-style', 'default');
        html.setAttribute('data-nav-layout', 'vertical');
        localStorage.removeItem('zenonavstyles');
        toggleSidemenu();
        localStorage.removeItem("zenoverticalstyles");
        document.querySelectorAll(".main-menu>li.open").forEach((ele) => {
            if (!ele.classList.contains('active')) {
                ele.classList.remove('open');
                ele.querySelector('ul').style.display = 'none';
            }
        });
        updateThemeSetting('menu_behavior', 'default');
        updateThemeSetting('layout', 'vertical');
    });
    if (closedBtn) closedBtn.addEventListener('click', () => {
        closedSidemenuFn();
        themeSetItem("zenoverticalstyles", 'closed');
        document.querySelectorAll(".main-menu>li.open").forEach((ele) => {
            if (!ele.classList.contains('active')) {
                ele.classList.remove('open');
                ele.querySelector('ul').style.display = 'none';
            }
        });
        updateThemeSetting('menu_behavior', 'closed');
    });
    if (detachedBtn) detachedBtn.addEventListener('click', () => {
        detachedFn();
        themeSetItem("zenoverticalstyles", 'detached');
        updateThemeSetting('menu_behavior', 'detached');
    });
    if (iconTextBtn) iconTextBtn.addEventListener('click', () => {
        iconTextFn();
        themeSetItem("zenoverticalstyles", 'icontext');
        updateThemeSetting('menu_behavior', 'icontext');
    });
    if (overlayBtn) overlayBtn.addEventListener('click', () => {
        iconOverayFn();
        themeSetItem("zenoverticalstyles", 'overlay');
        document.querySelectorAll(".main-menu>li.open").forEach((ele) => {
            if (!ele.classList.contains('active')) {
                ele.classList.remove('open');
                ele.querySelector('ul').style.display = 'none';
            }
        });
        updateThemeSetting('menu_behavior', 'overlay');
    });
    if (doubleBtn) doubleBtn.addEventListener('click', () => {
        doubletFn();
        localStorage.removeItem('zenonavstyles');
        themeSetItem("zenoverticalstyles", 'doublemenu');
        updateThemeSetting('menu_behavior', 'doublemenu');
    });
    if (menuClickBtn) menuClickBtn.addEventListener('click', () => {
        html.removeAttribute('data-vertical-style');
        menuClickFn();
        themeSetItem("zenonavstyles", 'menu-click');
        localStorage.removeItem("zenoverticalstyles");
        document.querySelectorAll(".main-menu>li.open").forEach((ele) => {
            if (!ele.classList.contains('active')) {
                ele.classList.remove('open');
                ele.querySelector('ul').style.display = 'none';
            }
        });
        if (document.querySelector("html").getAttribute("data-nav-layout") == 'horizontal') {
            document.querySelector(".main-menu").style.marginLeft = "0px";
            document.querySelector(".main-menu").style.marginRight = "0px";
            ResizeMenu();
        }
        updateThemeSetting('menu_behavior', 'menu-click');
    });
    if (menuHoverBtn) menuHoverBtn.addEventListener('click', () => {
        html.removeAttribute('data-vertical-style');
        menuhoverFn();
        themeSetItem("zenonavstyles", 'menu-hover');
        localStorage.removeItem("zenoverticalstyles");
        if (document.querySelector("html").getAttribute("data-nav-layout") == 'horizontal') {
            document.querySelector(".main-menu").style.marginLeft = "0px";
            document.querySelector(".main-menu").style.marginRight = "0px";
            ResizeMenu();
        }
        updateThemeSetting('menu_behavior', 'menu-hover');
    });
    if (iconClickBtn) iconClickBtn.addEventListener('click', () => {
        html.removeAttribute('data-vertical-style');
        iconClickFn();
        themeSetItem("zenonavstyles", 'icon-click');
        localStorage.removeItem("zenoverticalstyles");

        if (document.querySelector("html").getAttribute("data-nav-layout") == 'horizontal') {
            document.querySelector(".main-menu").style.marginLeft = "0px";
            document.querySelector(".main-menu").style.marginRight = "0px";
            ResizeMenu();
            document.querySelector("#slide-left").classList.add("d-none");
        }
        document.querySelectorAll(".main-menu>li.open").forEach((ele) => {
            if (!ele.classList.contains('active')) {
                ele.classList.remove('open');
                ele.querySelector('ul').style.display = 'none';
            }
        });
        updateThemeSetting('menu_behavior', 'icon-click');
    });
    /* icon hover Sidemenu Start */
    if (iconHoverBtn) iconHoverBtn.addEventListener('click', () => {
        html.removeAttribute('data-vertical-style');
        iconHoverFn();
        themeSetItem("zenonavstyles", 'icon-hover');
        localStorage.removeItem("zenoverticalstyles");
        if (document.querySelector("html").getAttribute("data-nav-layout") == 'horizontal') {
            document.querySelector(".main-menu").style.marginLeft = "0px";
            document.querySelector(".main-menu").style.marginRight = "0px";
            ResizeMenu();
            document.querySelector("#slide-left").classList.add("d-none");
        }
        updateThemeSetting('menu_behavior', 'icon-hover');
    });

    /* Sidemenu start*/
    if (verticalBtn) verticalBtn.addEventListener('click', () => {
        let mainContent = document.querySelector('.main-content');
        // local storage
        localStorage.removeItem("zenolayout");
        themeSetItem("zenoverticalstyles", 'default');
        verticalFn();
        setNavActive();
        mainContent.removeEventListener('click', clearNavDropdown);

        //
        document.querySelector(".main-menu").style.marginLeft = "0px"
        document.querySelector(".main-menu").style.marginRight = "0px"

        document.querySelectorAll(".slide").forEach((element) => {
            if (
                element.classList.contains("open") &&
                !element.classList.contains("active")
            ) {
                element.querySelector("ul").style.display = "none";
            }
        });
        updateThemeSetting('layout', 'vertical');
    });
    /* Sidemenu end */

    /* horizontal start*/
    if (horiBtn) horiBtn.addEventListener('click', () => {
        let mainContent = document.querySelector('.main-content');
        html.removeAttribute('data-vertical-style');
        //    local storage
        themeSetItem("zenolayout", 'horizontal');
        localStorage.removeItem("zenoverticalstyles");

        horizontalClickFn();
        clearNavDropdown();
        mainContent.addEventListener('click', clearNavDropdown);
        updateThemeSetting('layout', 'horizontal');
    });
    /* horizontal end*/  

    // reset all start
    let resetVar = ResetAll.addEventListener('click', () => {
        ResetAllFn();
        setNavActive();
        document.querySelector("html").setAttribute("data-menu-styles", "light");
        document.querySelector("html").setAttribute("data-width", "fullwidth");
        document.querySelector('#switcher-menu-light').checked = true;
        document.querySelectorAll(".slide").forEach((element) => {
            if (
                element.classList.contains("open") &&
                !element.classList.contains("active")
            ) {
                element.querySelector("ul").style.display = "none";
            }
        });
        if (document.querySelector(".noUi-target")) {
            document.querySelectorAll(".noUi-origin").forEach((e) => {
                e.classList.add("transform-none");
            });
        }
    })
    // reset all end

    /* loader start */
    if (loaderEnable) loaderEnable.onclick = () => {
        document.querySelector("html").setAttribute("loader", "enable");
        themeSetItem("loaderEnable", "true");
        updateThemeSetting('loader', 'enable');
    };
    if (loaderDisable) loaderDisable.onclick = () => {
        document.querySelector("html").setAttribute("loader", "disable");
        themeSetItem("loaderEnable", "false");
        updateThemeSetting('loader', 'disable');
    };
    /* loader end */
} 

function lightFn() {
    let html = document.querySelector('html');
    html.setAttribute('data-theme-mode', 'light');
    html.setAttribute('data-header-styles', 'light');
    html.setAttribute('data-menu-styles', 'light');
    if (!localStorage.getItem('primaryRGB')) {
        html.setAttribute('style', '')
    }
    document.querySelector('#switcher-light-theme').checked = true;
    document.querySelector('#switcher-menu-light').checked = true;
    document.querySelector('#switcher-header-light').checked = true;
    localStorage.removeItem("zenodarktheme");
    localStorage.removeItem("zenobgColor");
    localStorage.removeItem("zenoheaderbg");
    localStorage.removeItem("zenobgwhite");
    localStorage.removeItem("zenomenubg");
    localStorage.removeItem("zenomenubg");
    checkOptions();
    html.style.removeProperty('--body-bg-rgb');
    html.style.removeProperty('--body-bg-rgb2');
    html.style.removeProperty("--light-rgb");
    html.style.removeProperty("--form-control-bg");
    html.style.removeProperty("--gray-3");
    html.style.removeProperty("--input-border");

    document.querySelector("#switcher-background4").checked = false
    document.querySelector("#switcher-background3").checked = false
    document.querySelector("#switcher-background2").checked = false
    document.querySelector("#switcher-background1").checked = false
    document.querySelector("#switcher-background").checked = false
    document.querySelector('#switcher-menu-light').checked = true;
    document.querySelector('#switcher-header-light').checked = true;

}

function darkFn() {
    let html = document.querySelector('html');
    html.setAttribute('data-theme-mode', 'dark');
    html.setAttribute('data-header-styles', 'dark');
    html.setAttribute('data-menu-styles', 'dark');
    if (!localStorage.getItem('primaryRGB')) {
        html.setAttribute('style', '')
    }
    document.querySelector('#switcher-menu-dark').checked = true;
    document.querySelector('#switcher-header-dark').checked = true;
    document.querySelector('html').style.removeProperty('--body-bg-rgb');
    document.querySelector('html').style.removeProperty('--body-bg-rgb2');
    document.querySelector('html').style.removeProperty('--light-rgb');
    document.querySelector('html').style.removeProperty('--form-control-bg');
    document.querySelector('html').style.removeProperty('--gray-3');
    document.querySelector('html').style.removeProperty('--input-border');
    themeSetItem("zenodarktheme", true,true);
    localStorage.removeItem("zenolighttheme");
    localStorage.removeItem("bodyBgRGB");
    localStorage.removeItem("zenobgColor");
    localStorage.removeItem("zenoheaderbg");
    localStorage.removeItem("zenobgwhite");
    localStorage.removeItem("zenomenubg");
    checkOptions();

    document.querySelector("#switcher-background4").checked = false
    document.querySelector("#switcher-background3").checked = false
    document.querySelector("#switcher-background2").checked = false
    document.querySelector("#switcher-background1").checked = false
    document.querySelector("#switcher-background").checked = false
    document.querySelector('#switcher-menu-dark').checked = true;
    document.querySelector('#switcher-header-dark').checked = true;
}

function verticalFn() {
    let html = document.querySelector('html');
    html.setAttribute('data-nav-layout', 'vertical');
    html.setAttribute('data-vertical-style', 'overlay');
    html.removeAttribute('data-nav-style');
    localStorage.removeItem('zenonavstyles');
    html.removeAttribute('data-toggled');
    document.querySelector('#switcher-vertical').checked = true;
    document.querySelector('#switcher-menu-click').checked = false;
    document.querySelector('#switcher-menu-hover').checked = false;
    document.querySelector('#switcher-icon-click').checked = false;
    document.querySelector('#switcher-icon-hover').checked = false;
    checkOptions();
}

function horizontalClickFn() {
    document.querySelector('#switcher-horizontal').checked = true;
    document.querySelector('#switcher-menu-click').checked = true;
    let html = document.querySelector('html');
    html.setAttribute('data-nav-layout', 'horizontal');
    html.removeAttribute('data-vertical-style');
    if (!html.getAttribute('data-nav-style')) {
        html.setAttribute('data-nav-style', 'menu-click');
    }
    if (!localStorage.zenoMenu && !localStorage.bodylightRGB) {
        html.setAttribute("data-menu-styles", "light")
        document.querySelector('#switcher-menu-light').checked = true;
        checkOptions();
    }
    checkOptions();
    checkHoriMenu();
}


function ResetAllFn() {
    let html = document.querySelector('html');
    if (localStorage.getItem("zenolayout") == "horizontal") {
        document.querySelector(".main-menu").style.display = "block"
    }
    checkOptions();

    // clearing localstorage
    localStorage.clear();

    // reseting to light
    lightFn();

    //To reset the light-rgb
    document.querySelector('html').removeAttribute("style")

    // clearing attibutes
    // removing header, menu, pageStyle & boxed
    html.removeAttribute('data-nav-style');
    html.removeAttribute('data-menu-position');
    html.removeAttribute('data-header-position');
    html.removeAttribute('data-page-style');

    // removing theme styles
    html.removeAttribute('data-bg-img');

    // clear primary & bg color
    html.style.removeProperty(`--primary-rgb`);
    html.style.removeProperty(`--body-bg-rgb`); 

    // reseting to vertical
    verticalFn();
    mainContent.removeEventListener('click', clearNavDropdown);

    // reseting page style
    document.querySelector('#switcher-classic').checked = false;
    document.querySelector('#switcher-modern').checked = false;
    document.querySelector('#switcher-regular').checked = true;

    // reseting layout width styles
    document.querySelector('#switcher-full-width').checked = true;
    document.querySelector('#switcher-boxed').checked = false;

    // reseting menu position styles
    document.querySelector('#switcher-menu-fixed').checked = true;
    document.querySelector('#switcher-menu-scroll').checked = false;

    // reseting header position styles
    document.querySelector('#switcher-header-fixed').checked = true;
    document.querySelector('#switcher-header-scroll').checked = false;

    // reseting sidemenu layout styles
    document.querySelector('#switcher-default-menu').checked = true;
    document.querySelector('#switcher-closed-menu').checked = false;
    document.querySelector('#switcher-icontext-menu').checked = false;
    document.querySelector('#switcher-icon-overlay').checked = false;
    document.querySelector('#switcher-detached').checked = false;
    document.querySelector('#switcher-double-menu').checked = false;

    // resetting theme primary
    document.querySelector("#switcher-primary").checked = false;
    document.querySelector("#switcher-primary1").checked = false;
    document.querySelector("#switcher-primary2").checked = false;
    document.querySelector("#switcher-primary3").checked = false;
    document.querySelector("#switcher-primary4").checked = false;

    // resetting theme background
    document.querySelector("#switcher-background").checked = false;
    document.querySelector("#switcher-background1").checked = false;
    document.querySelector("#switcher-background2").checked = false;
    document.querySelector("#switcher-background3").checked = false;
    document.querySelector("#switcher-background4").checked = false;

    // persist reset to DB (direction and theme_mode stay in localStorage only)
    updateThemeSettingsMulti({
        layout: 'vertical',
        width: 'fullwidth',
        header_style: 'light',
        menu_style: 'light',
        page_style: 'regular',
        header_position: 'fixed',
        menu_position: 'fixed',
        menu_behavior: 'default',
        primary_color: '',
        background_color: '',
        background_light_color: '',
        loader: 'disable'
    });

    // to reset horizontal menu scroll
    document.querySelector(".main-menu").style.marginLeft = "0px"
    document.querySelector(".main-menu").style.marginRight = "0px"

    setTimeout(() => {
        document.querySelectorAll(".slide").forEach((element) => {
            if (element && document.querySelector(".child3 .side-menu__item.active")) {
                element.closest('ul.slide-menu').style.display = "block"
                element.closest('ul.slide-menu').closest('li.slide.has-sub').classList.add('open')
                element.closest('ul.slide-menu').closest('li.slide.has-sub').querySelector('.side-menu__item').classList.add('active')
                element.closest('ul.slide-menu').closest('li.slide.has-sub').querySelector('.child2 .has-sub.active').classList.add("open")
            }
        });
    }, 100);
}

function checkOptions() {
    // Only direction and lighting (theme mode) from localStorage; all other options come from DB / applyThemeSettingsFromDatabase
    if (localStorage.getItem('zenodarktheme')) {
        const darkRadio = document.querySelector('#switcher-dark-theme');
        if (darkRadio) darkRadio.checked = true;
    } else {
        const lightRadio = document.querySelector('#switcher-light-theme');
        if (lightRadio) lightRadio.checked = true;
    } 
}

// Restore ONLY theme mode (light/dark) from localStorage. Do NOT restore colors, loader, or layout — those come from DB.
function localStorageBackup2() {
    const isDark = localStorage.getItem('zenodarktheme');
    if (isDark) {
        const darkRadio = document.querySelector('#switcher-dark-theme');
        if (darkRadio) darkRadio.checked = true;
        const menuDark = document.querySelector('#switcher-menu-dark');
        if (menuDark) menuDark.checked = true;
        const headerDark = document.querySelector('#switcher-header-dark');
        if (headerDark) headerDark.checked = true;
        const html = document.querySelector('html');
        html.setAttribute('data-theme-mode', 'dark');
        html.setAttribute('data-menu-styles', 'dark');
        html.setAttribute('data-header-styles', 'dark');
        // Background colors stay from DB (already applied); do not overwrite with localStorage
    }
    // Loader comes from DB only; do not restore from localStorage here
}



