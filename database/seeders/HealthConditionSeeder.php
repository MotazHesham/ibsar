<?php

namespace Database\Seeders;

use App\Models\HealthCondition;
use Illuminate\Database\Seeder;

class HealthConditionSeeder extends Seeder
{
    public function run()
    {
        $healthConditions = [ 
            [
                'name' => ['ar' => 'مرض السكري', 'en' => 'Diabetes'],
            ],
            [
                'name' => ['ar' => 'ارتفاع ضغط الدم', 'en' => 'Hypertension'],
            ],
            [
                'name' => ['ar' => 'أمراض القلب', 'en' => 'Heart Disease'],
            ],
            [
                'name' => ['ar' => 'الربو', 'en' => 'Asthma'],
            ],
            [
                'name' => ['ar' => 'التهاب المفاصل', 'en' => 'Arthritis'],
            ],
            [
                'name' => ['ar' => 'أمراض الكلی', 'en' => 'Kidney Disease'],
            ],
            [
                'name' => ['ar' => 'أمراض الكبد', 'en' => 'Liver Disease'],
            ],
            [
                'name' => ['ar' => 'السرطان', 'en' => 'Cancer'],
            ],
            [
                'name' => ['ar' => 'الاكتئاب', 'en' => 'Depression'],
            ],
            [
                'name' => ['ar' => 'القلق', 'en' => 'Anxiety'],
            ],
            [
                'name' => ['ar' => 'الصرع', 'en' => 'Epilepsy'],
            ],
            [
                'name' => ['ar' => 'مرض الزهايمر', 'en' => 'Alzheimer\'s Disease'],
            ],
            [
                'name' => ['ar' => 'هشاشة العظام', 'en' => 'Osteoporosis'],
            ],
            [
                'name' => ['ar' => 'أمراض الغدة الدرقية', 'en' => 'Thyroid Disease'],
            ],
            [
                'name' => ['ar' => 'أخرى', 'en' => 'Other'],
            ], 
        ];

        foreach ($healthConditions as $healthCondition) {
            HealthCondition::create($healthCondition);
        }
    }
} 