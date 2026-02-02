<?php

namespace Database\Seeders;

use App\Models\ServiceStatus;
use Illuminate\Database\Seeder;

class ServiceStatusSeeder extends Seeder
{
    public function run()
    {
        $serviceStatuses = [
            [
                'name' => ['ar' => 'تم قبول الطلب وسيتم تقديم الخدمة', 'en' => 'Request Accepted'],
                'badge_class' => 'primary',
                'ordering' => 1,
            ],
            [
                'name' => ['ar' => 'تم احالة الطلب للأخصائية', 'en' => 'Referral to Specialist'],
                'badge_class' => 'primary',
                'ordering' => 2,
            ],
            [
                'name' => ['ar' => 'تم استلام الطلب وجاري دراسته', 'en' => 'Under Study'],
                'badge_class' => 'primary',
                'ordering' => 3,
            ], 
        ];

        foreach ($serviceStatuses as $serviceStatus) {
            ServiceStatus::create($serviceStatus);
        }
    }
} 