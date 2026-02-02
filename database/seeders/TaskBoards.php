<?php

namespace Database\Seeders;

use App\Models\TaskBoard; 
use Illuminate\Database\Seeder;

class TaskBoards extends Seeder
{
    public function run()
    {
        $taskBoards = [
            [
                'id'   => 1,
                'name' => 'شراء مستلزمات', 
            ], 
            [
                'id'   => 2,
                'name' => 'تصميم الموقع', 
            ],
            [
                'id'   => 3,
                'name' => 'كتابة المحتوى', 
            ],
            [
                'id'   => 4,
                'name' => 'دعم العملاء', 
            ],
            [
                'id'   => 5,
                'name' => 'إدارة المشاريع', 
            ],
            [
                'id'   => 6,
                'name' => 'الموارد البشرية', 
            ],
        ];

        TaskBoard::insert($taskBoards);
    }
}
