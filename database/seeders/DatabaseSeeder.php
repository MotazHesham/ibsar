<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            PermissionRoleTableSeeder::class,
            UsersTableSeeder::class,
            RoleUserTableSeeder::class,
            TaskStatusTableSeeder::class,
            GenerativeNumberSeeder::class,
            NationalitiesSeeder::class,
            MaritalStatusSeeder::class,
            AccommodationTypeSeeder::class,
            FamilyRelationshipSeeder::class,
            EducationalQualificationSeeder::class,
            DisabilityTypeSeeder::class,
            HealthConditionSeeder::class,
            EconomicStatusSeeder::class,
            ServiceStatusSeeder::class, 
            RequiredDocumentSeeder::class,
            JobTypeSeeder::class,
            TaskPrioritySeeder::class,
            TaskTagsSeeder::class,
            TaskBoards::class,
            ServiceSeeder::class,
            BeneficiaryFieldVisibilitySettingsSeeder::class,
            SettingSeeder::class,
            RegionSeeder::class,
            CitiesSeeder::class, 
        ]);
    }
}
