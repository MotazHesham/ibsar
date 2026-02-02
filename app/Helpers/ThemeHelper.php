<?php

namespace App\Helpers;

use App\Models\Setting;

/**
 * Theme settings from database only (no cache).
 * Direction and theme_mode (light/dark) are NOT included here — they are stored in localStorage only on the client.
 */
class ThemeHelper
{
    public static function getThemeSettings()
    { 

        return Setting::where('group_name', 'theme_settings')
            ->pluck('value', 'key')
            ->toArray();
    }

    public static function applyThemeSettings()
    {
        $settings = self::getThemeSettings();
        $htmlAttributes = [];
        $cssVariables = [];

        // Do NOT add theme_mode or direction — those are localStorage-only on client

        if (isset($settings['layout'])) {
            $htmlAttributes['data-nav-layout'] = $settings['layout'];
        }

        if (isset($settings['width'])) {
            $htmlAttributes['data-width'] = $settings['width'];
        }

        if (isset($settings['header_style'])) {
            $htmlAttributes['data-header-styles'] = $settings['header_style'];
        }

        if (isset($settings['menu_style'])) {
            $htmlAttributes['data-menu-styles'] = $settings['menu_style'];
        }

        if (isset($settings['page_style'])) {
            $htmlAttributes['data-page-style'] = $settings['page_style'];
        }

        if (isset($settings['header_position'])) {
            $htmlAttributes['data-header-position'] = $settings['header_position'];
        }

        if (isset($settings['menu_position'])) {
            $htmlAttributes['data-menu-position'] = $settings['menu_position'];
        }

        if (isset($settings['menu_behavior'])) {
            $val = $settings['menu_behavior'];
            if (in_array($val, ['default', 'closed', 'detached', 'icontext', 'overlay', 'doublemenu'], true)) {
                $htmlAttributes['data-vertical-style'] = $val;
            }
            if (in_array($val, ['menu-click', 'menu-hover', 'icon-click', 'icon-hover'], true)) {
                $htmlAttributes['data-nav-style'] = $val;
            }
        }

        if (isset($settings['loader'])) {
            $htmlAttributes['loader'] = $settings['loader'];
        }

        if (!empty($settings['primary_color'])) {
            $cssVariables['--primary-rgb'] = $settings['primary_color'];
        }

        if (!empty($settings['background_color'])) {
            $cssVariables['--body-bg-rgb'] = $settings['background_color'];
        }

        if (!empty($settings['background_light_color'])) {
            $cssVariables['--body-bg-rgb2'] = $settings['background_light_color'];
            $cssVariables['--light-rgb'] = $settings['background_light_color'];
            $cssVariables['--form-control-bg'] = 'rgb(' . $settings['background_light_color'] . ')';
            $cssVariables['--input-border'] = 'rgba(255,255,255,0.1)';
            $cssVariables['--gray-3'] = 'rgba(255,255,255,0.1)';
        }

        return [
            'attributes' => $htmlAttributes,
            'css_variables' => $cssVariables,
            'settings' => $settings,
        ];
    }

    public static function getCheckedStates()
    {
        $settings = self::getThemeSettings();
        $checkedStates = [];

        if (isset($settings['layout'])) {
            $checkedStates['layout'] = $settings['layout'];
        }
        if (isset($settings['width'])) {
            $checkedStates['width'] = $settings['width'];
        }
        if (isset($settings['header_style'])) {
            $checkedStates['header_style'] = $settings['header_style'];
        }
        if (isset($settings['menu_style'])) {
            $checkedStates['menu_style'] = $settings['menu_style'];
        }
        if (isset($settings['page_style'])) {
            $checkedStates['page_style'] = $settings['page_style'];
        }
        if (isset($settings['header_position'])) {
            $checkedStates['header_position'] = $settings['header_position'];
        }
        if (isset($settings['menu_position'])) {
            $checkedStates['menu_position'] = $settings['menu_position'];
        }
        if (isset($settings['menu_behavior'])) {
            $checkedStates['menu_behavior'] = $settings['menu_behavior'];
        }
        if (isset($settings['primary_color'])) {
            $checkedStates['primary_color'] = $settings['primary_color'];
        }
        if (isset($settings['background_color'])) {
            $checkedStates['background_color'] = $settings['background_color'];
        }
        if (isset($settings['loader'])) {
            $checkedStates['loader'] = $settings['loader'];
        }

        return $checkedStates;
    }
}
