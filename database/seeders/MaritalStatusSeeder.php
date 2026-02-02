<?php

namespace Database\Seeders;

use App\Models\MaritalStatus;
use Illuminate\Database\Seeder;

class MaritalStatusSeeder extends Seeder
{
    public function run()
    {
        $maritalStatuses = [ 
            [
                'name' => ['ar' => 'مطلقة', 'en' => 'Divorced'],
            ],
            [
                'name' => ['ar' => 'أرملة', 'en' => 'Widowed'],
            ], 
        ];

        foreach ($maritalStatuses as $maritalStatus) {
            MaritalStatus::create($maritalStatus);
        }
    }
} 