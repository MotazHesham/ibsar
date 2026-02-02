<?php

namespace Database\Seeders;

use App\Models\EducationalQualification;
use Illuminate\Database\Seeder;

class EducationalQualificationSeeder extends Seeder
{
    public function run()
    {
        $educationalQualifications = [
            [
                'name' => ['ar' => 'أمي', 'en' => 'Illiterate'],
            ],
            [
                'name' => ['ar' => 'يقرأ ويكتب', 'en' => 'Literate'],
            ],
            [
                'name' => ['ar' => 'ابتدائية', 'en' => 'Primary School'],
            ],
            [
                'name' => ['ar' => 'متوسطة', 'en' => 'Middle School'],
            ],
            [
                'name' => ['ar' => 'ثانوية', 'en' => 'High School'],
            ],
            [
                'name' => ['ar' => 'دبلوم', 'en' => 'Diploma'],
            ],
            [
                'name' => ['ar' => 'بكالوريوس', 'en' => 'Bachelor\'s Degree'],
            ],
            [
                'name' => ['ar' => 'ماجستير', 'en' => 'Master\'s Degree'],
            ],
            [
                'name' => ['ar' => 'دكتوراه', 'en' => 'PhD'],
            ],
            [
                'name' => ['ar' => 'دبلوم عالي', 'en' => 'Higher Diploma'],
            ],
            [
                'name' => ['ar' => 'شهادة مهنية', 'en' => 'Professional Certificate'],
            ],
            [
                'name' => ['ar' => 'دبلوم تقني', 'en' => 'Technical Diploma'],
            ],
            [
                'name' => ['ar' => 'بكالوريوس تقني', 'en' => 'Technical Bachelor\'s'],
            ],
        ];

        foreach ($educationalQualifications as $educationalQualification) {
            EducationalQualification::create($educationalQualification);
        }
    }
} 