<?php

namespace Database\Seeders;

use App\Models\TaskTag; 
use Illuminate\Database\Seeder;

class TaskTagsSeeder extends Seeder
{
    public function run()
    {
        $taskTags = [
            [
                'id'   => 1,
                'name' => 'شراء مواد',
                'badge_class' => 'badge bg-success-transparent',
            ],
            [
                'id'   => 2,
                'name' => 'تصميم',
                'badge_class' => 'badge bg-primary-transparent',
            ],
            [
                'id'   => 3,
                'name' => 'تطوير',
                'badge_class' => 'badge bg-danger-transparent',
            ],   
            [
                'id'   => 4,
                'name' => 'محتوي توثيق',
                'badge_class' => 'badge bg-warning-transparent',
            ], 
            [
                'id'   => 5,
                'name' => 'محتوي للمشاركة',
                'badge_class' => 'badge bg-info-transparent',
            ], 
        ];

        TaskTag::insert($taskTags);
    }
}
