<?php

namespace Database\Seeders;

use App\Models\AccommodationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccommodationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
        $accommodationTypes = [
            [
                'id'    => 1,
                'name' => [
                    'ar' => 'إيجار',
                    'en' => 'rental',
                ],
            ],
            [
                'id'    => 2,
                'name' => [
                    'ar' => 'ملك',
                    'en' => 'own',
                ],
            ],
            [
                'id'    => 3,
                'name' => [
                    'ar' => 'سكن تنموي',
                    'en' => 'social housing',
                ],
            ],
            [
                'id'    => 4,
                'name' => [
                    'ar' => 'سكن خيري',
                    'en' => 'charity housing',
                ],
            ],
            [
                'id'    => 5,
                'name' => [
                    'ar' => 'سكن مع الأهل',
                    'en' => 'with parents',
                ],
            ],
        ];
        foreach ($accommodationTypes as $accommodationType) {
            AccommodationType::create($accommodationType);
        }
    }
}
