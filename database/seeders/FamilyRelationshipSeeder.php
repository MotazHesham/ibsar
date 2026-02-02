<?php

namespace Database\Seeders;

use App\Models\FamilyRelationship;
use Illuminate\Database\Seeder;

class FamilyRelationshipSeeder extends Seeder
{
    public function run()
    {
        $familyRelationships = [
            [
                'name' => ['ar' => 'أب', 'en' => 'Father'],
                'gender' => 'male',
            ],
            [
                'name' => ['ar' => 'أم', 'en' => 'Mother'],
                'gender' => 'female',
            ],
            [
                'name' => ['ar' => 'ابن', 'en' => 'Son'],
                'gender' => 'male',
            ],
            [
                'name' => ['ar' => 'ابنة', 'en' => 'Daughter'],
                'gender' => 'female',
            ],
            [
                'name' => ['ar' => 'أخ', 'en' => 'Brother'],
                'gender' => 'male',
            ],
            [
                'name' => ['ar' => 'أخت', 'en' => 'Sister'],
                'gender' => 'female',
            ],
            [
                'name' => ['ar' => 'زوج', 'en' => 'Husband'],
                'gender' => 'male',
            ],
            [
                'name' => ['ar' => 'زوجة', 'en' => 'Wife'],
                'gender' => 'female',
            ],
            [
                'name' => ['ar' => 'جد', 'en' => 'Grandfather'],
                'gender' => 'male',
            ],
            [
                'name' => ['ar' => 'جدة', 'en' => 'Grandmother'],
                'gender' => 'female',
            ],
            [
                'name' => ['ar' => 'عم', 'en' => 'Paternal Uncle'],
                'gender' => 'male',
            ],
            [
                'name' => ['ar' => 'عمة', 'en' => 'Paternal Aunt'],
                'gender' => 'female',
            ],
            [
                'name' => ['ar' => 'خال', 'en' => 'Maternal Uncle'],
                'gender' => 'male',
            ],
            [
                'name' => ['ar' => 'خالة', 'en' => 'Maternal Aunt'],
                'gender' => 'female',
            ],
            [
                'name' => ['ar' => 'ابن الأخ', 'en' => 'Nephew'],
                'gender' => 'male',
            ],
            [
                'name' => ['ar' => 'ابنة الأخ/الأخت', 'en' => 'Niece'],
                'gender' => 'female',
            ],
            [
                'name' => ['ar' => 'حفيد', 'en' => 'Grandson'],
                'gender' => 'male',
            ],
            [
                'name' => ['ar' => 'حفيدة', 'en' => 'Granddaughter'],
                'gender' => 'female',
            ],
        ];

        foreach ($familyRelationships as $familyRelationship) {
            FamilyRelationship::create($familyRelationship);
        }
    }
} 