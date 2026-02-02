<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;
class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // General
        Setting::create([ 
            'name' => 'Site Name', 'key' => 'site_name', 'options' => null, 'value' => 'Mostafed', 'lang' => null, 'type' => 'string',
            'group_name' => 'general', 'order_level' => 1, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Site Logo', 'key' => 'site_logo', 'options' => null, 'value' => null, 'lang' => null, 'type' => 'file',
            'group_name' => 'general', 'order_level' => 2, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Site Address', 'key' => 'site_address', 'options' => null, 'value' => 'Site Address', 'lang' => null, 'type' => 'string',
            'group_name' => 'general', 'order_level' => 3, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Site Phone', 'key' => 'site_phone', 'options' => null, 'value' => 'Site Phone', 'lang' => null, 'type' => 'string',
            'group_name' => 'general', 'order_level' => 4, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Site Email', 'key' => 'site_email', 'options' => null, 'value' => 'Site Email', 'lang' => null, 'type' => 'string',
            'group_name' => 'general', 'order_level' => 5, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Site Login Text', 'key' => 'site_login_text', 'options' => null, 'value' => 'Site Login Text', 'lang' => null, 'type' => 'string',
            'group_name' => 'general', 'order_level' => 6, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Site Working Hours', 'key' => 'site_working_hours', 'options' => null, 'value' => 'Site Working Hours', 'lang' => null, 'type' => 'string',
            'group_name' => 'general', 'order_level' => 6, 'grid_col' => 6,
        ]);    
        $auto_accept_options = ['yes' => 'Yes', 'no' => 'No'];
        Setting::create([ 
            'name' => 'Auto Accept', 'key' => 'auto_accept_beneficiary', 'options' => json_encode($auto_accept_options), 'value' => 'no', 'lang' => null, 'type' => 'radio',
            'group_name' => 'general', 'order_level' => 7, 'grid_col' => 6,
        ]); 
        $beneficiary_form_steps = ['login_information' => 'Login Information', 'basic_information' => 'Basic Information', 'work_information' => 'Work Information', 'family_information' => 'Family Information', 'economic_information' => 'Economic Information', 'documents' => 'Documents'];
        Setting::create([ 
            'name' => 'Enable Request Order After Beneficiary Status', 'key' => 'enable_request_order_after_beneficiary_status', 'options' => json_encode($beneficiary_form_steps), 'value' => 'basic_information', 'lang' => null, 'type' => 'select',
            'group_name' => 'general', 'order_level' => 8, 'grid_col' => 6,
        ]);
        $enable_request_for_family_members_options = ['yes' => 'Yes', 'no' => 'No'];
        Setting::create([ 
            'name' => 'Enable Request for Family Members', 'key' => 'enable_request_for_family_members', 'options' => json_encode($enable_request_for_family_members_options), 'value' => 'no', 'lang' => null, 'type' => 'radio',
            'group_name' => 'general', 'order_level' => 9, 'grid_col' => 6,
        ]); 
        $enable_get_signature_from_beneficiary_options = ['yes' => 'Yes', 'no' => 'No'];
        Setting::create([ 
            'name' => 'Enable Get Signature From Beneficiary', 'key' => 'enable_get_signature_from_beneficiary', 'options' => json_encode($enable_get_signature_from_beneficiary_options), 'value' => 'no', 'lang' => null, 'type' => 'radio',
            'group_name' => 'general', 'order_level' => 10, 'grid_col' => 6,
        ]); 
        Setting::create([ 
            'name' => 'Login Cover', 'key' => 'login_cover', 'options' => null, 'value' => null, 'lang' => null, 'type' => 'file',
            'group_name' => 'general', 'order_level' => 11, 'grid_col' => 6,
        ]);    

        // Social Media
        Setting::create([ 
            'name' => 'Facebook', 'key' => 'facebook', 'options' => null, 'value' => 'https://www.facebook.com', 'lang' => null, 'type' => 'string',
            'group_name' => 'social_media', 'order_level' => 1, 'grid_col' => 4,
        ]);
        Setting::create([ 
            'name' => 'Instagram', 'key' => 'instagram', 'options' => null, 'value' => 'https://www.instagram.com', 'lang' => null, 'type' => 'string',
            'group_name' => 'social_media', 'order_level' => 2, 'grid_col' => 4,
        ]);
        Setting::create([ 
            'name' => 'Twitter', 'key' => 'twitter', 'options' => null, 'value' => 'https://www.twitter.com', 'lang' => null, 'type' => 'string',
            'group_name' => 'social_media', 'order_level' => 3, 'grid_col' => 4,
        ]);
        Setting::create([ 
            'name' => 'Youtube', 'key' => 'youtube', 'options' => null, 'value' => 'https://www.youtube.com', 'lang' => null, 'type' => 'string',
            'group_name' => 'social_media', 'order_level' => 4, 'grid_col' => 4,
        ]);
        Setting::create([ 
            'name' => 'Tiktok', 'key' => 'tiktok', 'options' => null, 'value' => 'https://www.tiktok.com', 'lang' => null, 'type' => 'string',
            'group_name' => 'social_media', 'order_level' => 5, 'grid_col' => 4,
        ]); 
        Setting::create([ 
            'name' => 'Linkedin', 'key' => 'linkedin', 'options' => null, 'value' => 'https://www.linkedin.com', 'lang' => null, 'type' => 'string',
            'group_name' => 'social_media', 'order_level' => 6, 'grid_col' => 4,
        ]); 
        Setting::create([ 
            'name' => 'Whatsapp', 'key' => 'whatsapp', 'options' => null, 'value' => 'https://www.whatsapp.com', 'lang' => null, 'type' => 'string',
            'group_name' => 'social_media', 'order_level' => 7, 'grid_col' => 4,
        ]); 

        // home_about
        Setting::create([ 
            'name' => 'Home About Why Choose Us Text', 'key' => 'home_about_why_choose_us_text', 'options' => null, 'value' => null, 'lang' => 'ar', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 1, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home About Why Choose Us Text', 'key' => 'home_about_why_choose_us_text', 'options' => null, 'value' => null, 'lang' => 'en', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 1, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home About Why Choose Us Title 1', 'key' => 'home_about_why_choose_use_1_title', 'options' => null, 'value' => null, 'lang' => 'ar', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 2, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home About Why Choose Us Title 1', 'key' => 'home_about_why_choose_use_1_title', 'options' => null, 'value' => null, 'lang' => 'en', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 2, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home About Why Choose Us Sub Title 1', 'key' => 'home_about_why_choose_use_1_sub_title', 'options' => null, 'value' => null, 'lang' => 'ar', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 3, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home About Why Choose Us Sub Title 1', 'key' => 'home_about_why_choose_use_1_sub_title', 'options' => null, 'value' => null, 'lang' => 'en', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 3, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home About Why Choose Us Title 2', 'key' => 'home_about_why_choose_use_2_title', 'options' => null, 'value' => null, 'lang' => 'ar', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 4, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home About Why Choose Us Title 2', 'key' => 'home_about_why_choose_use_2_title', 'options' => null, 'value' => null, 'lang' => 'en', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 4, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home About Why Choose Us Sub Title 2', 'key' => 'home_about_why_choose_use_2_sub_title', 'options' => null, 'value' => null, 'lang' => 'ar', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 5, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home About Why Choose Us Sub Title 2', 'key' => 'home_about_why_choose_use_2_sub_title', 'options' => null, 'value' => null, 'lang' => 'en', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 5, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home About Why Choose Us Title 3', 'key' => 'home_about_why_choose_use_3_title', 'options' => null, 'value' => null, 'lang' => 'ar', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 4, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home About Why Choose Us Title 3', 'key' => 'home_about_why_choose_use_3_title', 'options' => null, 'value' => null, 'lang' => 'en', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 4, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home About Why Choose Us Sub Title 3', 'key' => 'home_about_why_choose_use_3_sub_title', 'options' => null, 'value' => null, 'lang' => 'ar', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 5, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home About Why Choose Us Sub Title 3', 'key' => 'home_about_why_choose_use_3_sub_title', 'options' => null, 'value' => null, 'lang' => 'en', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 5, 'grid_col' => 6,
        ]);    
        
        // home projects
        Setting::create([ 
            'name' => 'Home projects Title', 'key' => 'home_projects_title', 'options' => null, 'value' => null, 'lang' => 'ar', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 1, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home projects Title', 'key' => 'home_projects_title', 'options' => null, 'value' => null, 'lang' => 'en', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 1, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home projects Sub Title', 'key' => 'home_projects_sub_title', 'options' => null, 'value' => null, 'lang' => 'ar', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 2, 'grid_col' => 6,
        ]);     
        Setting::create([ 
            'name' => 'Home projects Sub Title', 'key' => 'home_projects_sub_title', 'options' => null, 'value' => null, 'lang' => 'en', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 2, 'grid_col' => 6,
        ]);     

        // home partners
        Setting::create([ 
            'name' => 'Home partners Title', 'key' => 'home_partners_title', 'options' => null, 'value' => null, 'lang' => 'ar', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 1, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home partners Title', 'key' => 'home_partners_title', 'options' => null, 'value' => null, 'lang' => 'en', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 1, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home partners Sub Title', 'key' => 'home_partners_sub_title', 'options' => null, 'value' => null, 'lang' => 'ar', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 2, 'grid_col' => 6,
        ]);     
        Setting::create([ 
            'name' => 'Home partners Sub Title', 'key' => 'home_partners_sub_title', 'options' => null, 'value' => null, 'lang' => 'en', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 2, 'grid_col' => 6,
        ]);     

        // home reviews
        Setting::create([ 
            'name' => 'Home reviews Title', 'key' => 'home_reviews_title', 'options' => null, 'value' => null, 'lang' => 'ar', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 1, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home reviews Title', 'key' => 'home_reviews_title', 'options' => null, 'value' => null, 'lang' => 'en', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 1, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home reviews Sub Title', 'key' => 'home_reviews_sub_title', 'options' => null, 'value' => null, 'lang' => 'ar', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 2, 'grid_col' => 6,
        ]);     
        Setting::create([ 
            'name' => 'Home reviews Sub Title', 'key' => 'home_reviews_sub_title', 'options' => null, 'value' => null, 'lang' => 'en', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 2, 'grid_col' => 6,
        ]);     

        // home contact
        Setting::create([ 
            'name' => 'Home contact Title', 'key' => 'home_contact_title', 'options' => null, 'value' => null, 'lang' => 'ar', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 1, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home contact Title', 'key' => 'home_contact_title', 'options' => null, 'value' => null, 'lang' => 'en', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 1, 'grid_col' => 6,
        ]);    
        Setting::create([ 
            'name' => 'Home contact Sub Title', 'key' => 'home_contact_sub_title', 'options' => null, 'value' => null, 'lang' => 'ar', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 2, 'grid_col' => 6,
        ]);     
        Setting::create([ 
            'name' => 'Home contact Sub Title', 'key' => 'home_contact_sub_title', 'options' => null, 'value' => null, 'lang' => 'en', 'type' => 'string',
            'group_name' => 'home_setting', 'order_level' => 2, 'grid_col' => 6,
        ]);     

        // footer settings
        Setting::create([ 
            'name' => 'Footer Logo', 'key' => 'footer_logo', 'options' => null, 'value' => null, 'lang' => null, 'type' => 'file',
            'group_name' => 'footer_setting', 'order_level' => 1, 'grid_col' => 12,
        ]);      
        Setting::create([ 
            'name' => 'Footer Description', 'key' => 'footer_description', 'options' => null, 'value' => 'Footer Description', 'lang' => 'ar', 'type' => 'string',
            'group_name' => 'footer_setting', 'order_level' => 2, 'grid_col' => 12,
        ]);  
        Setting::create([ 
            'name' => 'Footer Description', 'key' => 'footer_description', 'options' => null, 'value' => 'Footer Description', 'lang' => 'en', 'type' => 'string',
            'group_name' => 'footer_setting', 'order_level' => 2, 'grid_col' => 12,
        ]);  
        
        

        // Pusher Settings
        Setting::create([ 
            'name' => 'Pusher Key', 'key' => 'pusher_key', 'options' => null, 'value' => 'pusher_key', 'lang' => null, 'type' => 'string',
            'group_name' => 'pusher_settings', 'order_level' => 1, 'grid_col' => 6,
        ]); 
        Setting::create([ 
            'name' => 'Pusher Secret', 'key' => 'pusher_secret', 'options' => null, 'value' => 'pusher_secret', 'lang' => null, 'type' => 'string',
            'group_name' => 'pusher_settings', 'order_level' => 1, 'grid_col' => 6,
        ]); 
        Setting::create([ 
            'name' => 'Pusher App ID', 'key' => 'pusher_app_id', 'options' => null, 'value' => 'pusher_app_id', 'lang' => null, 'type' => 'string',
            'group_name' => 'pusher_settings', 'order_level' => 1, 'grid_col' => 6,
        ]); 
        Setting::create([ 
            'name' => 'Pusher Cluster', 'key' => 'pusher_cluster', 'options' => null, 'value' => 'pusher_cluster', 'lang' => null, 'type' => 'string',
            'group_name' => 'pusher_settings', 'order_level' => 1, 'grid_col' => 6,
        ]); 

        // Theme Settings
        $theme_mode_options = ['light' => 'Light Mode', 'dark' => 'Dark Mode'];
        Setting::create([ 
            'name' => 'Theme Mode', 'key' => 'theme_mode', 'options' => json_encode($theme_mode_options), 'value' => 'light', 'lang' => null, 'type' => 'select',
            'group_name' => 'theme_settings', 'order_level' => 1, 'grid_col' => 6,
        ]); 

        $layout_options = ['vertical' => 'Vertical Layout', 'horizontal' => 'Horizontal Layout'];
        Setting::create([ 
            'name' => 'Layout', 'key' => 'layout', 'options' => json_encode($layout_options), 'value' => 'vertical', 'lang' => null, 'type' => 'select',
            'group_name' => 'theme_settings', 'order_level' => 2, 'grid_col' => 6,
        ]); 

        $direction_options = ['ltr' => 'Left to Right', 'rtl' => 'Right to Left'];
        Setting::create([ 
            'name' => 'Direction', 'key' => 'direction', 'options' => json_encode($direction_options), 'value' => 'ltr', 'lang' => null, 'type' => 'select',
            'group_name' => 'theme_settings', 'order_level' => 3, 'grid_col' => 6,
        ]); 

        $width_options = ['fullwidth' => 'Full Width', 'boxed' => 'Boxed'];
        Setting::create([ 
            'name' => 'Width', 'key' => 'width', 'options' => json_encode($width_options), 'value' => 'fullwidth', 'lang' => null, 'type' => 'select',
            'group_name' => 'theme_settings', 'order_level' => 4, 'grid_col' => 6,
        ]); 

        $header_style_options = ['light' => 'Light Header', 'dark' => 'Dark Header', 'color' => 'Color Header', 'gradient' => 'Gradient Header', 'transparent' => 'Transparent Header'];
        Setting::create([ 
            'name' => 'Header Style', 'key' => 'header_style', 'options' => json_encode($header_style_options), 'value' => 'light', 'lang' => null, 'type' => 'select',
            'group_name' => 'theme_settings', 'order_level' => 5, 'grid_col' => 6,
        ]); 

        $menu_style_options = ['light' => 'Light Menu', 'dark' => 'Dark Menu', 'color' => 'Color Menu', 'gradient' => 'Gradient Menu', 'transparent' => 'Transparent Menu'];
        Setting::create([ 
            'name' => 'Menu Style', 'key' => 'menu_style', 'options' => json_encode($menu_style_options), 'value' => 'light', 'lang' => null, 'type' => 'select',
            'group_name' => 'theme_settings', 'order_level' => 6, 'grid_col' => 6,
        ]); 

        $page_style_options = ['regular' => 'Regular', 'classic' => 'Classic', 'modern' => 'Modern'];
        Setting::create([ 
            'name' => 'Page Style', 'key' => 'page_style', 'options' => json_encode($page_style_options), 'value' => 'regular', 'lang' => null, 'type' => 'select',
            'group_name' => 'theme_settings', 'order_level' => 7, 'grid_col' => 6,
        ]); 

        $header_position_options = ['fixed' => 'Fixed Header', 'scrollable' => 'Scrollable Header'];
        Setting::create([ 
            'name' => 'Header Position', 'key' => 'header_position', 'options' => json_encode($header_position_options), 'value' => 'fixed', 'lang' => null, 'type' => 'select',
            'group_name' => 'theme_settings', 'order_level' => 8, 'grid_col' => 6,
        ]); 

        $menu_position_options = ['fixed' => 'Fixed Menu', 'scrollable' => 'Scrollable Menu'];
        Setting::create([ 
            'name' => 'Menu Position', 'key' => 'menu_position', 'options' => json_encode($menu_position_options), 'value' => 'fixed', 'lang' => null, 'type' => 'select',
            'group_name' => 'theme_settings', 'order_level' => 9, 'grid_col' => 6,
        ]); 

        $menu_behavior_options = ['default' => 'Default Menu', 'closed' => 'Closed Menu', 'detached' => 'Detached Menu', 'icontext' => 'Icon Text Menu', 'overlay' => 'Icon Overlay Menu', 'doublemenu' => 'Double Menu', 'menu-click' => 'Menu Click', 'menu-hover' => 'Menu Hover', 'icon-click' => 'Icon Click', 'icon-hover' => 'Icon Hover'];
        Setting::create([ 
            'name' => 'Menu Behavior', 'key' => 'menu_behavior', 'options' => json_encode($menu_behavior_options), 'value' => 'default', 'lang' => null, 'type' => 'select',
            'group_name' => 'theme_settings', 'order_level' => 10, 'grid_col' => 6,
        ]); 

        Setting::create([ 
            'name' => 'Primary Color', 'key' => 'primary_color', 'options' => null, 'value' => '', 'lang' => null, 'type' => 'string',
            'group_name' => 'theme_settings', 'order_level' => 11, 'grid_col' => 6,
        ]); 

        Setting::create([ 
            'name' => 'Background Color', 'key' => 'background_color', 'options' => null, 'value' => '', 'lang' => null, 'type' => 'string',
            'group_name' => 'theme_settings', 'order_level' => 12, 'grid_col' => 6,
        ]); 

        Setting::create([ 
            'name' => 'Background Light Color', 'key' => 'background_light_color', 'options' => null, 'value' => '', 'lang' => null, 'type' => 'string',
            'group_name' => 'theme_settings', 'order_level' => 13, 'grid_col' => 6,
        ]); 

        $loader_options = ['enable' => 'Enable Loader', 'disable' => 'Disable Loader'];
        Setting::create([ 
            'name' => 'Loader', 'key' => 'loader', 'options' => json_encode($loader_options), 'value' => 'disable', 'lang' => null, 'type' => 'select',
            'group_name' => 'theme_settings', 'order_level' => 14, 'grid_col' => 6,
        ]); 


        // Odoo Settings
        Setting::create([ 
            'name' => 'Odoo URL', 'key' => 'odoo_url', 'options' => null, 'value' => 'https://odoo.com', 'lang' => null, 'type' => 'string',
            'group_name' => 'odoo_settings', 'order_level' => 1, 'grid_col' => 6,
        ]); 
        
        Setting::create([ 
            'name' => 'Odoo DB', 'key' => 'odoo_db', 'options' => null, 'value' => 'odoo', 'lang' => null, 'type' => 'string',
            'group_name' => 'odoo_settings', 'order_level' => 1, 'grid_col' => 6,
        ]); 
        
        
        Setting::create([ 
            'name' => 'Odoo Username', 'key' => 'odoo_username', 'options' => null, 'value' => 'odoo', 'lang' => null, 'type' => 'string',
            'group_name' => 'odoo_settings', 'order_level' => 1, 'grid_col' => 6,
        ]);  
        
        Setting::create([ 
            'name' => 'Odoo Password', 'key' => 'odoo_password', 'options' => null, 'value' => 'odoo', 'lang' => null, 'type' => 'string',
            'group_name' => 'odoo_settings', 'order_level' => 1, 'grid_col' => 6,
        ]);   
        $oddo_activation_options = ['enable' => 'Enable', 'disable' => 'Disable'];
        Setting::create([ 
            'name' => 'Odoo Activation', 'key' => 'odoo_activation', 'options' => json_encode($oddo_activation_options), 'value' => 'disable', 'lang' => null, 'type' => 'select',
            'group_name' => 'odoo_settings', 'order_level' => 1, 'grid_col' => 6,
        ]); 
        
            
    }
}
