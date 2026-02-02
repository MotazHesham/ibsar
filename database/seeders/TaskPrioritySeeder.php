<?php

namespace Database\Seeders;

use App\Models\TaskPriority; 
use Illuminate\Database\Seeder;

class TaskPrioritySeeder extends Seeder
{
    public function run()
    {
        $taskPriorites = [
            [
                'id'   => 1,
                'name' => 'أهمية عالية',
                'badge_class' => 'badge bg-danger-transparent',
            ],
            [
                'id'   => 2,
                'name' => 'أهمية متوسطة',
                'badge_class' => 'badge bg-primary-transparent',
            ],
            [
                'id'   => 3,
                'name' => 'أهمية منخفضة',
                'badge_class' => 'badge bg-warning-transparent',
            ], 
        ];

        TaskPriority::insert($taskPriorites);
    }
}
