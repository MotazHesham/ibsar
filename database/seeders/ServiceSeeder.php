<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::create([
            'type' => 'loan', 
            'key_name' => 'individual_loan', 
            'title' => 'قرض فردي', 
            'active' => 1, 
        ]);
        Service::create([
            'type' => 'loan', 
            'key_name' => 'group_loan', 
            'title' => 'قرض جماعي', 
            'active' => 1, 
        ]);
    }
}
