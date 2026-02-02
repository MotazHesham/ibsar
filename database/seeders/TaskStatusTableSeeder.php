<?php

namespace Database\Seeders;

use App\Models\TaskStatus;
use Illuminate\Database\Seeder;

class TaskStatusTableSeeder extends Seeder
{
    public function run()
    {
        $taskStatuses = [
            [
                'id'   => 1,
                'name' => 'جديد',
                'badge_class' => 'primary',
            ],
            [
                'id'   => 2,
                'name' => 'قيد التنفيذ',
                'badge_class' => 'success',
            ],
            [
                'id'   => 3,
                'name' => 'جاري التنفيذ',
                'badge_class' => 'info',
            ],
            [
                'id'   => 4,
                'name' => 'قيد المراجعة',
                'badge_class' => 'secondary',
            ],
            [
                'id'   => 5,
                'name' => 'منجز',
                'badge_class' => 'warning',
            ],
        ];

        TaskStatus::insert($taskStatuses);
    }
}
