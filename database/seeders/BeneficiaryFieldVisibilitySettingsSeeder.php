<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BeneficiaryFieldVisibilitySetting;

class BeneficiaryFieldVisibilitySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fields = [
            // Basic Information Fields
            ['field_name' => 'name', 'field_group' => 'basic_information', 'field_label' => 'Name', 'is_visible' => true, 'is_required' => true, 'description' => 'Full name of the beneficiary', 'sort_order' => 1],
            ['field_name' => 'nationality_id', 'field_group' => 'basic_information', 'field_label' => 'Nationality', 'is_visible' => true, 'is_required' => true, 'description' => 'Nationality of the beneficiary', 'sort_order' => 2],
            ['field_name' => 'characteristic_of_nationality', 'field_group' => 'basic_information', 'field_label' => 'Characteristic of Nationality', 'is_visible' => true, 'is_required' => false, 'description' => 'Characteristic of nationality (Saudi, Other, Son of Citizen, etc.)', 'sort_order' => 3],
            ['field_name' => 'dob', 'field_group' => 'basic_information', 'field_label' => 'Date of Birth', 'is_visible' => true, 'is_required' => false, 'description' => 'Date of birth', 'sort_order' => 4],
            ['field_name' => 'marital_status_id', 'field_group' => 'basic_information', 'field_label' => 'Marital Status', 'is_visible' => true, 'is_required' => true, 'description' => 'Marital status', 'sort_order' => 5],
            ['field_name' => 'martial_status_date', 'field_group' => 'basic_information', 'field_label' => 'Marital Status Date', 'is_visible' => true, 'is_required' => false, 'description' => 'Date of marital status change', 'sort_order' => 6],
            ['field_name' => 'city_id', 'field_group' => 'basic_information', 'field_label' => 'City', 'is_visible' => true, 'is_required' => true, 'description' => 'City of residence', 'sort_order' => 7],
            ['field_name' => 'district_id', 'field_group' => 'basic_information', 'field_label' => 'District', 'is_visible' => true, 'is_required' => true, 'description' => 'District of residence', 'sort_order' => 8],
            ['field_name' => 'street', 'field_group' => 'basic_information', 'field_label' => 'Street', 'is_visible' => true, 'is_required' => true, 'description' => 'Street address', 'sort_order' => 9],
            ['field_name' => 'building_number', 'field_group' => 'basic_information', 'field_label' => 'Building Number', 'is_visible' => true, 'is_required' => true, 'description' => 'Building number', 'sort_order' => 10],
            ['field_name' => 'building_additional_number', 'field_group' => 'basic_information', 'field_label' => 'Building Additional Number', 'is_visible' => true, 'is_required' => true, 'description' => 'Additional building number', 'sort_order' => 11],
            ['field_name' => 'postal_code', 'field_group' => 'basic_information', 'field_label' => 'Postal Code', 'is_visible' => true, 'is_required' => true, 'description' => 'Postal code', 'sort_order' => 12],
            ['field_name' => 'address', 'field_group' => 'basic_information', 'field_label' => 'Full Address', 'is_visible' => true, 'is_required' => true, 'description' => 'Complete address', 'sort_order' => 13],
            ['field_name' => 'map', 'field_group' => 'basic_information', 'field_label' => 'Map Location', 'is_visible' => true, 'is_required' => false, 'description' => 'Map coordinates', 'sort_order' => 14],
            ['field_name' => 'beneficiary_category_id', 'field_group' => 'basic_information', 'field_label' => 'Beneficiary Category', 'is_visible' => false, 'is_required' => false, 'description' => 'Beneficiary category', 'sort_order' => 15],

            // Login Information Fields
            ['field_name' => 'identity_num', 'field_group' => 'login_information', 'field_label' => 'Identity Number', 'is_visible' => true, 'is_required' => true, 'description' => 'National identity number', 'sort_order' => 1],
            ['field_name' => 'email', 'field_group' => 'login_information', 'field_label' => 'Email', 'is_visible' => true, 'is_required' => false, 'description' => 'Email address', 'sort_order' => 2],
            ['field_name' => 'phone', 'field_group' => 'login_information', 'field_label' => 'Phone', 'is_visible' => true, 'is_required' => true, 'description' => 'Primary phone number', 'sort_order' => 3],
            ['field_name' => 'phone_2', 'field_group' => 'login_information', 'field_label' => 'Secondary Phone', 'is_visible' => true, 'is_required' => false, 'description' => 'Secondary phone number', 'sort_order' => 4],
            ['field_name' => 'password', 'field_group' => 'login_information', 'field_label' => 'Password', 'is_visible' => true, 'is_required' => false, 'description' => 'Account password', 'sort_order' => 5],
            ['field_name' => 'region_id', 'field_group' => 'login_information', 'field_label' => 'Region', 'is_visible' => true, 'is_required' => true, 'description' => 'Region of residence', 'sort_order' => 6],
            ['field_name' => 'photo', 'field_group' => 'login_information', 'field_label' => 'Photo', 'is_visible' => true, 'is_required' => false, 'description' => 'Profile photo', 'sort_order' => 7],

            // Work Information Fields
            ['field_name' => 'educational_qualification_id', 'field_group' => 'work_information', 'field_label' => 'Educational Qualification', 'is_visible' => true, 'is_required' => true, 'description' => 'Educational qualification level', 'sort_order' => 1],
            ['field_name' => 'job_type_id', 'field_group' => 'work_information', 'field_label' => 'Job Type', 'is_visible' => true, 'is_required' => true, 'description' => 'Type of employment', 'sort_order' => 2],
            ['field_name' => 'company_name', 'field_group' => 'work_information', 'field_label' => 'Company Name', 'is_visible' => true, 'is_required' => false, 'description' => 'Name of employer company', 'sort_order' => 3],
            ['field_name' => 'job_title', 'field_group' => 'work_information', 'field_label' => 'Job Title', 'is_visible' => true, 'is_required' => false, 'description' => 'Job position title', 'sort_order' => 4],
            ['field_name' => 'job_phone', 'field_group' => 'work_information', 'field_label' => 'Job Phone', 'is_visible' => true, 'is_required' => false, 'description' => 'Work phone number', 'sort_order' => 5],
            ['field_name' => 'job_address', 'field_group' => 'work_information', 'field_label' => 'Job Address', 'is_visible' => true, 'is_required' => false, 'description' => 'Work address', 'sort_order' => 6],
            ['field_name' => 'has_health_condition', 'field_group' => 'work_information', 'field_label' => 'Has Health Condition', 'is_visible' => true, 'is_required' => true, 'description' => 'Whether beneficiary has health conditions', 'sort_order' => 7],
            ['field_name' => 'health_condition_id', 'field_group' => 'work_information', 'field_label' => 'Health Condition', 'is_visible' => true, 'is_required' => false, 'description' => 'Specific health condition', 'sort_order' => 8],
            ['field_name' => 'custom_health_condition', 'field_group' => 'work_information', 'field_label' => 'Custom Health Condition', 'is_visible' => true, 'is_required' => false, 'description' => 'Custom health condition description', 'sort_order' => 9],
            ['field_name' => 'has_disability', 'field_group' => 'work_information', 'field_label' => 'Has Disability', 'is_visible' => true, 'is_required' => true, 'description' => 'Whether beneficiary has disabilities', 'sort_order' => 10],
            ['field_name' => 'disability_type_id', 'field_group' => 'work_information', 'field_label' => 'Disability Type', 'is_visible' => true, 'is_required' => false, 'description' => 'Type of disability', 'sort_order' => 11],
            ['field_name' => 'custom_disability_type', 'field_group' => 'work_information', 'field_label' => 'Custom Disability Type', 'is_visible' => true, 'is_required' => false, 'description' => 'Custom disability type description', 'sort_order' => 12],
            ['field_name' => 'can_work', 'field_group' => 'work_information', 'field_label' => 'Can Work', 'is_visible' => true, 'is_required' => true, 'description' => 'Work capability status', 'sort_order' => 13],

            // Economic Information Fields
            ['field_name' => 'incomes', 'field_group' => 'economic_information', 'field_label' => 'Incomes', 'is_visible' => true, 'is_required' => false, 'description' => 'Income sources and amounts', 'sort_order' => 1],
            ['field_name' => 'expenses', 'field_group' => 'economic_information', 'field_label' => 'Expenses', 'is_visible' => true, 'is_required' => false, 'description' => 'Monthly expenses', 'sort_order' => 2],
            ['field_name' => 'accommodation_type_id', 'field_group' => 'economic_information', 'field_label' => 'Accommodation Type', 'is_visible' => true, 'is_required' => true, 'description' => 'Type of accommodation', 'sort_order' => 3],
            ['field_name' => 'accommodation_entity_charity_id', 'field_group' => 'economic_information', 'field_label' => 'Charity Entity', 'is_visible' => true, 'is_required' => false, 'description' => 'Charity accommodation entity', 'sort_order' => 4],
            ['field_name' => 'accommodation_entity_social_id', 'field_group' => 'economic_information', 'field_label' => 'Social Entity', 'is_visible' => true, 'is_required' => false, 'description' => 'Social accommodation entity', 'sort_order' => 5],
            ['field_name' => 'accommodation_rent', 'field_group' => 'economic_information', 'field_label' => 'Accommodation Rent', 'is_visible' => true, 'is_required' => false, 'description' => 'Monthly rent amount', 'sort_order' => 6],
            ['field_name' => 'accommodation_rent_late', 'field_group' => 'economic_information', 'field_label' => 'Late Rent', 'is_visible' => true, 'is_required' => false, 'description' => 'Late rent amount', 'sort_order' => 7],

            // Documents Fields
            ['field_name' => 'documents', 'field_group' => 'documents', 'field_label' => 'Required Documents', 'is_visible' => true, 'is_required' => false, 'description' => 'Required document uploads', 'sort_order' => 1],

            // Family Information Fields
            ['field_name' => 'family_information', 'field_group' => 'family_information', 'field_label' => 'Family Information', 'is_visible' => true, 'is_required' => false, 'description' => 'Family member details', 'sort_order' => 1],
        ];

        foreach ($fields as $field) {
            BeneficiaryFieldVisibilitySetting::create($field);
        }
    }
}