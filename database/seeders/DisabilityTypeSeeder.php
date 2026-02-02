<?php

namespace Database\Seeders;

use App\Models\DisabilityType;
use Illuminate\Database\Seeder;

class DisabilityTypeSeeder extends Seeder
{
    public function run()
    {
        $disabilityTypes = [ 
            [
                'name' => ['ar' => 'إعاقة حركية', 'en' => 'Physical Disability'],
            ],
            [
                'name' => ['ar' => 'إعاقة بصرية', 'en' => 'Visual Impairment'],
            ],
            [
                'name' => ['ar' => 'إعاقة سمعية', 'en' => 'Hearing Impairment'],
            ],
            [
                'name' => ['ar' => 'إعاقة ذهنية', 'en' => 'Intellectual Disability'],
            ],
            [
                'name' => ['ar' => 'إعاقة نطق', 'en' => 'Speech Disability'],
            ],
            [
                'name' => ['ar' => 'إعاقة متعددة', 'en' => 'Multiple Disabilities'],
            ],
            [
                'name' => ['ar' => 'اضطراب طيف التوحد', 'en' => 'Autism Spectrum Disorder'],
            ],
            [
                'name' => ['ar' => 'صعوبات التعلم', 'en' => 'Learning Disabilities'],
            ],
            [
                'name' => ['ar' => 'إعاقة نفسية', 'en' => 'Psychiatric Disability'],
            ],
            [
                'name' => ['ar' => 'الشلل الدماغي', 'en' => 'Cerebral Palsy'],
            ],
            [
                'name' => ['ar' => 'إصابات الحبل الشوكي', 'en' => 'Spinal Cord Injuries'],
            ],
            [
                'name' => ['ar' => 'بتر الأطراف', 'en' => 'Limb Amputation'],
            ],
            [
                'name' => ['ar' => 'أخرى', 'en' => 'Other'],
            ], 
        ];

        foreach ($disabilityTypes as $disabilityType) {
            DisabilityType::create($disabilityType);
        }
    }
} 