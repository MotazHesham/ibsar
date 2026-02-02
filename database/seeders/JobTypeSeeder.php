<?php

namespace Database\Seeders;

use App\Models\JobType;
use Illuminate\Database\Seeder;

class JobTypeSeeder extends Seeder
{
    public function run()
    {
        $jobTypes = [
            [
                'name' => ['ar' => 'دوام كامل', 'en' => 'Full-time'],
            ],
            [
                'name' => ['ar' => 'دوام جزئي', 'en' => 'Part-time'],
            ],
            [
                'name' => ['ar' => 'عقد مؤقت', 'en' => 'Contract'],
            ],
            [
                'name' => ['ar' => 'مستقل', 'en' => 'Freelance'],
            ],
            [
                'name' => ['ar' => 'متدرب', 'en' => 'Internship'],
            ],
            [
                'name' => ['ar' => 'عمل عن بُعد', 'en' => 'Remote Work'],
            ],
            [
                'name' => ['ar' => 'عمل مؤقت', 'en' => 'Temporary'],
            ],
            [
                'name' => ['ar' => 'عمل موسمي', 'en' => 'Seasonal'],
            ],
            [
                'name' => ['ar' => 'استشاري', 'en' => 'Consultant'],
            ],
            [
                'name' => ['ar' => 'متطوع', 'en' => 'Volunteer'],
            ],
            [
                'name' => ['ar' => 'عاطل عن العمل', 'en' => 'Unemployed'],
            ],
            [
                'name' => ['ar' => 'متقاعد', 'en' => 'Retired'],
            ],
            [
                'name' => ['ar' => 'طالب', 'en' => 'Student'],
            ],
            [
                'name' => ['ar' => 'ربة منزل', 'en' => 'Homemaker'],
            ],
        ];

        foreach ($jobTypes as $jobType) {
            JobType::create($jobType);
        }
    }
} 