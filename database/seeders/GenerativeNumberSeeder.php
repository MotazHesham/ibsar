<?php

namespace Database\Seeders;

use App\Models\GenerativeNumber;
use App\Models\Task;
use App\Models\TaskTag;
use App\Models\User;
use Illuminate\Database\Seeder;

class GenerativeNumberSeeder extends Seeder
{
    public function run()
    {
        GenerativeNumber::create([
            'model' => 'IncomingLetter',
            'type' => 'inner',
            'prefix' => 'INI',
            'number' => 0,
        ]);
        GenerativeNumber::create([
            'model' => 'IncomingLetter',
            'type' => 'outer',
            'prefix' => 'INO',
            'number' => 0,
        ]);
        GenerativeNumber::create([
            'model' => 'OutgoingLetter',
            'type' => 'inner',
            'prefix' => 'OUTI',
            'number' => 0,
        ]);
        GenerativeNumber::create([
            'model' => 'OutgoingLetter',
            'type' => 'outer',
            'prefix' => 'OUTO',
            'number' => 0,
        ]);
    }
}
