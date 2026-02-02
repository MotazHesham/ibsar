
        <div class="offcanvas offcanvas-end" tabindex="-1" id="switcher-canvas" aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header border-bottom d-block p-0">
                <div class="d-flex align-items-center justify-content-between p-3">
                    <h5 class="offcanvas-title text-default" id="offcanvasRightLabel">Switcher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <nav class="border-top border-block-start-dashed">
                    <div class="nav nav-tabs nav-justified" id="switcher-main-tab" role="tablist">
                        <button class="nav-link active" id="switcher-home-tab" data-bs-toggle="tab" data-bs-target="#switcher-home"
                            type="button" role="tab" aria-controls="switcher-home" aria-selected="true">Theme Styles</button>
                        <button class="nav-link" id="switcher-profile-tab" data-bs-toggle="tab" data-bs-target="#switcher-profile"
                            type="button" role="tab" aria-controls="switcher-profile" aria-selected="false">Theme Colors</button>
                    </div>
                </nav>
            </div>
            <div class="offcanvas-body">
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active border-0" id="switcher-home" role="tabpanel" aria-labelledby="switcher-home-tab"
                        tabindex="0">
                        <div class="">
                            <p class="switcher-style-head">Theme Color Mode:</p>
                            <div class="row switcher-style gx-0">
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-light-theme">
                                            Light
                                        </label>
                                        <input class="form-check-input" type="radio" name="theme-style" id="switcher-light-theme" checked>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-dark-theme">
                                            Dark
                                        </label>
                                        <input class="form-check-input" type="radio" name="theme-style" id="switcher-dark-theme">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="">
                            <p class="switcher-style-head">Directions:</p>
                            <div class="row switcher-style gx-0">
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-ltr">
                                            LTR
                                        </label>
                                        <input class="form-check-input" type="radio" name="direction" id="switcher-ltr" checked>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-rtl">
                                            RTL
                                        </label>
                                        <input class="form-check-input" type="radio" name="direction" id="switcher-rtl">
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <div class="">
                            <p class="switcher-style-head">Navigation Styles:</p>
                            <div class="row switcher-style gx-0">
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-vertical">
                                            Vertical
                                        </label>
                                        <input class="form-check-input" type="radio" name="navigation-style" id="switcher-vertical"
                                            @if(isset($themeCheckedStates['layout']) && $themeCheckedStates['layout'] == 'vertical') checked @endif>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-horizontal">
                                            Horizontal
                                        </label>
                                        <input class="form-check-input" type="radio" name="navigation-style" id="switcher-horizontal"
                                            @if(isset($themeCheckedStates['layout']) && $themeCheckedStates['layout'] == 'horizontal') checked @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="navigation-menu-styles">
                            <p class="switcher-style-head">Vertical & Horizontal Menu Styles:</p>
                            <div class="row switcher-style gx-0 pb-2 gy-2">
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-menu-click">
                                            Menu Click
                                        </label>
                                        <input class="form-check-input" type="radio" name="navigation-menu-styles"
                                            id="switcher-menu-click"
                                            @if(isset($themeCheckedStates['menu_behavior']) && $themeCheckedStates['menu_behavior'] == 'menu-click') checked @endif>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-menu-hover">
                                            Menu Hover
                                        </label>
                                        <input class="form-check-input" type="radio" name="navigation-menu-styles" id="switcher-menu-hover"
                                            @if(isset($themeCheckedStates['menu_behavior']) && $themeCheckedStates['menu_behavior'] == 'menu-hover') checked @endif>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-icon-click">
                                            Icon Click
                                        </label>
                                        <input class="form-check-input" type="radio" name="navigation-menu-styles" id="switcher-icon-click"
                                            @if(isset($themeCheckedStates['menu_behavior']) && $themeCheckedStates['menu_behavior'] == 'icon-click') checked @endif>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-icon-hover">
                                            Icon Hover
                                        </label>
                                        <input class="form-check-input" type="radio" name="navigation-menu-styles" id="switcher-icon-hover"
                                            @if(isset($themeCheckedStates['menu_behavior']) && $themeCheckedStates['menu_behavior'] == 'icon-hover') checked @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sidemenu-layout-styles">
                            <p class="switcher-style-head">Sidemenu Layout Styles:</p>
                            <div class="row switcher-style gx-0 pb-2 gy-2">
                                <div class="col-sm-6">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-default-menu">
                                            Default Menu
                                        </label>
                                        <input class="form-check-input" type="radio" name="sidemenu-layout-styles"
                                            id="switcher-default-menu"
                                            @if(isset($themeCheckedStates['menu_behavior']) && $themeCheckedStates['menu_behavior'] == 'default') checked @endif>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-closed-menu">
                                            Closed Menu
                                        </label>
                                        <input class="form-check-input" type="radio" name="sidemenu-layout-styles" id="switcher-closed-menu"
                                            @if(isset($themeCheckedStates['menu_behavior']) && $themeCheckedStates['menu_behavior'] == 'closed') checked @endif>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-icontext-menu">
                                            Icon Text
                                        </label>
                                        <input class="form-check-input" type="radio" name="sidemenu-layout-styles" id="switcher-icontext-menu"
                                            @if(isset($themeCheckedStates['menu_behavior']) && $themeCheckedStates['menu_behavior'] == 'icontext') checked @endif>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-icon-overlay">
                                            Icon Overlay
                                        </label>
                                        <input class="form-check-input" type="radio" name="sidemenu-layout-styles" id="switcher-icon-overlay"
                                            @if(isset($themeCheckedStates['menu_behavior']) && $themeCheckedStates['menu_behavior'] == 'overlay') checked @endif>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-detached">
                                            Detached
                                        </label>
                                        <input class="form-check-input" type="radio" name="sidemenu-layout-styles" id="switcher-detached"
                                            @if(isset($themeCheckedStates['menu_behavior']) && $themeCheckedStates['menu_behavior'] == 'detached') checked @endif>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-double-menu">
                                            Double Menu
                                        </label>
                                        <input class="form-check-input" type="radio" name="sidemenu-layout-styles" id="switcher-double-menu"
                                            @if(isset($themeCheckedStates['menu_behavior']) && $themeCheckedStates['menu_behavior'] == 'doublemenu') checked @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <p class="switcher-style-head">Page Styles:</p>
                            <div class="row switcher-style gx-0">
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-regular">
                                            Regular
                                        </label>
                                        <input class="form-check-input" type="radio" name="page-styles" id="switcher-regular"
                                            @if(isset($themeCheckedStates['page_style']) && $themeCheckedStates['page_style'] == 'regular') checked @endif>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-classic">
                                            Classic
                                        </label>
                                        <input class="form-check-input" type="radio" name="page-styles" id="switcher-classic"
                                            @if(isset($themeCheckedStates['page_style']) && $themeCheckedStates['page_style'] == 'classic') checked @endif>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-modern">
                                            Modern
                                        </label>
                                        <input class="form-check-input" type="radio" name="page-styles" id="switcher-modern"
                                            @if(isset($themeCheckedStates['page_style']) && $themeCheckedStates['page_style'] == 'modern') checked @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <p class="switcher-style-head">Layout Width Styles:</p>
                            <div class="row switcher-style gx-0">
                                <div class="col-sm-4 col-6">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-full-width">
                                            Full Width
                                        </label>
                                        <input class="form-check-input" type="radio" name="layout-width" id="switcher-full-width"
                                            @if(!isset($themeCheckedStates['width']) || $themeCheckedStates['width'] == 'fullwidth') checked @endif>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-6">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-boxed">
                                            Boxed
                                        </label>
                                        <input class="form-check-input" type="radio" name="layout-width" id="switcher-boxed"
                                            @if(isset($themeCheckedStates['width']) && $themeCheckedStates['width'] == 'boxed') checked @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <p class="switcher-style-head">Menu Positions:</p>
                            <div class="row switcher-style gx-0">
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-menu-fixed">
                                            Fixed
                                        </label>
                                        <input class="form-check-input" type="radio" name="menu-positions" id="switcher-menu-fixed"
                                            @if(!isset($themeCheckedStates['menu_position']) || $themeCheckedStates['menu_position'] == 'fixed') checked @endif>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-menu-scroll">
                                            Scrollable
                                        </label>
                                        <input class="form-check-input" type="radio" name="menu-positions" id="switcher-menu-scroll"
                                            @if(isset($themeCheckedStates['menu_position']) && $themeCheckedStates['menu_position'] == 'scrollable') checked @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <p class="switcher-style-head">Header Positions:</p>
                            <div class="row switcher-style gx-0">
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-header-fixed">
                                            Fixed
                                        </label>
                                        <input class="form-check-input" type="radio" name="header-positions"
                                            id="switcher-header-fixed"
                                            @if(!isset($themeCheckedStates['header_position']) || $themeCheckedStates['header_position'] == 'fixed') checked @endif>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-header-scroll">
                                            Scrollable
                                        </label>
                                        <input class="form-check-input" type="radio" name="header-positions"
                                            id="switcher-header-scroll"
                                            @if(isset($themeCheckedStates['header_position']) && $themeCheckedStates['header_position'] == 'scrollable') checked @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <p class="switcher-style-head">Loader:</p>
                            <div class="row switcher-style gx-0">
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-loader-enable">
                                            Enable
                                        </label>
                                        <input class="form-check-input" type="radio" name="page-loader"
                                            id="switcher-loader-enable"
                                            @if(isset($themeCheckedStates['loader']) && $themeCheckedStates['loader'] == 'enable') checked @endif>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check switch-select">
                                        <label class="form-check-label" for="switcher-loader-disable">
                                            Disable
                                        </label>
                                        <input class="form-check-input" type="radio" name="page-loader"
                                            id="switcher-loader-disable"
                                            @if(!isset($themeCheckedStates['loader']) || $themeCheckedStates['loader'] == 'disable') checked @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade border-0" id="switcher-profile" role="tabpanel" aria-labelledby="switcher-profile-tab" tabindex="0">
                        <div>
                            <div class="theme-colors">
                                <p class="switcher-style-head">Menu Colors:</p>
                                <div class="d-flex switcher-style pb-2">
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-white" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Light Menu" type="radio" name="menu-colors"
                                            id="switcher-menu-light"
                                            @if(!isset($themeCheckedStates['menu_style']) || $themeCheckedStates['menu_style'] == 'light') checked @endif>
                                    </div>
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-dark" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Dark Menu" type="radio" name="menu-colors"
                                            id="switcher-menu-dark"
                                            @if(isset($themeCheckedStates['menu_style']) && $themeCheckedStates['menu_style'] == 'dark') checked @endif>
                                    </div>
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-primary" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Color Menu" type="radio" name="menu-colors"
                                            id="switcher-menu-primary"
                                            @if(isset($themeCheckedStates['menu_style']) && $themeCheckedStates['menu_style'] == 'color') checked @endif>
                                    </div>
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-gradient" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Gradient Menu" type="radio" name="menu-colors"
                                            id="switcher-menu-gradient"
                                            @if(isset($themeCheckedStates['menu_style']) && $themeCheckedStates['menu_style'] == 'gradient') checked @endif>
                                    </div>
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-transparent"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Transparent Menu"
                                            type="radio" name="menu-colors" id="switcher-menu-transparent"
                                            @if(isset($themeCheckedStates['menu_style']) && $themeCheckedStates['menu_style'] == 'transparent') checked @endif>
                                    </div>
                                </div>
                                <div class="px-4 pb-3 text-muted fs-11">Note:If you want to change color Menu dynamically change from below Theme Primary color picker</div>
                            </div>
                            <div class="theme-colors">
                                <p class="switcher-style-head">Header Colors:</p>
                                <div class="d-flex switcher-style pb-2">
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-white" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Light Header" type="radio" name="header-colors"
                                            id="switcher-header-light"
                                            @if(!isset($themeCheckedStates['header_style']) || $themeCheckedStates['header_style'] == 'light') checked @endif>
                                    </div>
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-dark" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Dark Header" type="radio" name="header-colors"
                                            id="switcher-header-dark"
                                            @if(isset($themeCheckedStates['header_style']) && $themeCheckedStates['header_style'] == 'dark') checked @endif>
                                    </div>
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-primary" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Color Header" type="radio" name="header-colors"
                                            id="switcher-header-primary"
                                            @if(isset($themeCheckedStates['header_style']) && $themeCheckedStates['header_style'] == 'color') checked @endif>
                                    </div>
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-gradient" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Gradient Header" type="radio" name="header-colors"
                                            id="switcher-header-gradient"
                                            @if(isset($themeCheckedStates['header_style']) && $themeCheckedStates['header_style'] == 'gradient') checked @endif>
                                    </div>
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-transparent" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Transparent Header" type="radio" name="header-colors"
                                            id="switcher-header-transparent"
                                            @if(isset($themeCheckedStates['header_style']) && $themeCheckedStates['header_style'] == 'transparent') checked @endif>
                                    </div>
                                </div>
                                <div class="px-4 pb-3 text-muted fs-11">Note:If you want to change color Header dynamically change from below Theme Primary color picker</div>
                            </div>
                            <div class="theme-colors">
                                <p class="switcher-style-head">Theme Primary:</p>
                                <div class="d-flex flex-wrap align-items-center switcher-style">
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-primary-1" type="radio"
                                            name="theme-primary" id="switcher-primary"
                                            @if(!isset($themeCheckedStates['primary_color']) || $themeCheckedStates['primary_color'] == '64, 100, 221') checked @endif>
                                    </div>
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-primary-2" type="radio"
                                            name="theme-primary" id="switcher-primary1"
                                            @if(isset($themeCheckedStates['primary_color']) && $themeCheckedStates['primary_color'] == '207, 117, 225') checked @endif>
                                    </div>
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-primary-3" type="radio" name="theme-primary"
                                            id="switcher-primary2"
                                            @if(isset($themeCheckedStates['primary_color']) && $themeCheckedStates['primary_color'] == '199, 89, 106') checked @endif>
                                    </div>
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-primary-4" type="radio" name="theme-primary"
                                            id="switcher-primary3"
                                            @if(isset($themeCheckedStates['primary_color']) && $themeCheckedStates['primary_color'] == '1, 159, 162') checked @endif>
                                    </div>
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-primary-5" type="radio" name="theme-primary"
                                            id="switcher-primary4"
                                            @if(isset($themeCheckedStates['primary_color']) && $themeCheckedStates['primary_color'] == '139, 149, 4') checked @endif>
                                    </div>
                                    {{-- Dynamic primary option when value is custom (from picker / DB) --}}
                                    <div id="switcher-primary-dynamic-wrap" class="form-check switch-select me-3 d-none"></div>
                                    <div class="form-check switch-select ps-0 mt-1 color-primary-light">
                                        <div class="theme-container-primary"></div>
                                        <div class="pickr-container-primary"  onchange="updateChartColor(this.value)"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="theme-colors">
                                <p class="switcher-style-head">Theme Background:</p>
                                <div class="d-flex flex-wrap align-items-center switcher-style">
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-bg-1" type="radio"
                                            name="theme-background" id="switcher-background"
                                            @if(!isset($themeCheckedStates['background_color']) || $themeCheckedStates['background_color'] == '34, 49, 153') checked @endif>
                                    </div>
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-bg-2" type="radio"
                                            name="theme-background" id="switcher-background1"
                                            @if(isset($themeCheckedStates['background_color']) && $themeCheckedStates['background_color'] == '147, 52, 150') checked @endif>
                                    </div>
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-bg-3" type="radio" name="theme-background"
                                            id="switcher-background2"
                                            @if(isset($themeCheckedStates['background_color']) && $themeCheckedStates['background_color'] == '135, 44, 47') checked @endif>
                                    </div>
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-bg-4" type="radio"
                                            name="theme-background" id="switcher-background3"
                                            @if(isset($themeCheckedStates['background_color']) && $themeCheckedStates['background_color'] == '3, 81, 60') checked @endif>
                                    </div>
                                    <div class="form-check switch-select me-3">
                                        <input class="form-check-input color-input color-bg-5" type="radio"
                                            name="theme-background" id="switcher-background4"
                                            @if(isset($themeCheckedStates['background_color']) && $themeCheckedStates['background_color'] == '73, 78, 1') checked @endif>
                                    </div>
                                    {{-- Dynamic background option when value is custom (from picker / DB) --}}
                                    <div id="switcher-background-dynamic-wrap" class="form-check switch-select me-3 d-none"></div>
                                    <div class="form-check switch-select ps-0 mt-1 tooltip-static-demo color-bg-transparent">
                                        <div class="theme-container-background"></div>
                                        <div class="pickr-container-background"></div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="menu-image mb-3">
                                <p class="switcher-style-head">Menu With Background Image:</p>
                                <div class="d-flex flex-wrap align-items-center switcher-style">
                                    <div class="form-check switch-select m-2">
                                        <input class="form-check-input bgimage-input bg-img1" type="radio"
                                            name="menu-background" id="switcher-bg-img">
                                    </div>
                                    <div class="form-check switch-select m-2">
                                        <input class="form-check-input bgimage-input bg-img2" type="radio"
                                            name="menu-background" id="switcher-bg-img1">
                                    </div>
                                    <div class="form-check switch-select m-2">
                                        <input class="form-check-input bgimage-input bg-img3" type="radio" name="menu-background"
                                            id="switcher-bg-img2">
                                    </div>
                                    <div class="form-check switch-select m-2">
                                        <input class="form-check-input bgimage-input bg-img4" type="radio"
                                            name="menu-background" id="switcher-bg-img3">
                                    </div>
                                    <div class="form-check switch-select m-2">
                                        <input class="form-check-input bgimage-input bg-img5" type="radio"
                                            name="menu-background" id="switcher-bg-img4">
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="d-flex justify-content-between canvas-footer flex-nowrap gap-2">
                        <a href="javascript:void(0);" id="reset-all" class="btn btn-danger text-nowrap flex-fill">Reset</a> 
                    </div>
                </div>
            </div>
        </div>