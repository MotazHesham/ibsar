<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskTag;
use App\Models\User;
use Illuminate\Database\Seeder;

class FakeDataSeeder extends Seeder
{
    public function run()
    {
        if(User::where('user_type', 'staff')->count() < 50){
            User::factory()->count(50)->create();
        } 

        Task::factory()->count(60)->create();  

        // attach tags to tasks
        $tags = TaskTag::all();
        $tasks = Task::all();
        foreach ($tasks as $task) {
            $task->tags()->attach($tags->random(rand(1, 3)));
        }

        // attach users to tasks
        $users = User::where('user_type', 'staff')->get();
        $tasks = Task::all();
        foreach ($tasks as $task) {
            $task->assigned_tos()->attach($users->random(rand(1, 5)));
        }
    }
}
