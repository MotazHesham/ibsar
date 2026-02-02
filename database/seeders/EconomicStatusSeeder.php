<?php

namespace Database\Seeders;

use App\Models\EconomicStatus;
use Illuminate\Database\Seeder;

class EconomicStatusSeeder extends Seeder
{
    public function run()
    {
        $economicStatuses = [
            // Income sources
            [
                'name' => ['ar' => 'راتب شهري', 'en' => 'Monthly Salary'],
                'type' => 'income',
                'data_type' => 'number',
                'is_required' => true,
                'order_level' => 10,
            ],
            [
                'name' => ['ar' => 'معاش تقاعدي', 'en' => 'Pension'],
                'type' => 'income',
                'data_type' => 'number',
                'is_required' => true,
                'order_level' => 9,
            ],
            [
                'name' => ['ar' => 'التأمينات الاجتماعية', 'en' => 'Social Insurance'],
                'type' => 'income',
                'data_type' => 'number',
                'is_required' => true,
                'order_level' => 8,
            ],
            [
                'name' => ['ar' => 'التاهيل الشامل', 'en' => 'General Support'],
                'type' => 'income',
                'data_type' => 'number',
                'is_required' => true,
                'order_level' => 7,
            ],
            [
                'name' => ['ar' => 'الضامن الاجتماعي', 'en' => 'Social Security'],
                'type' => 'income',
                'data_type' => 'number',
                'is_required' => true,
                'order_level' => 6,
            ],
            [
                'name' => ['ar' => 'حساب المواطن', 'en' => 'Citizen Account'],
                'type' => 'income',
                'data_type' => 'number',
                'is_required' => true,
                'order_level' => 5,
            ],
            [
                'name' => ['ar' => 'مصدر دخل أخر', 'en' => 'Other Income'],
                'type' => 'income',
                'data_type' => 'text',
                'is_required' => false,
                'order_level' => 4,
            ],
            [
                'name' => ['ar' => 'قيم الدخل', 'en' => 'Income Value'],
                'type' => 'income',
                'data_type' => 'number',
                'is_required' => false,
                'order_level' => 3,
            ],
            [
                'name' => ['ar' => 'الإعانة الشهرية', 'en' => 'Monthly Allowance'],
                'type' => 'income',
                'data_type' => 'text',
                'is_required' => false,
                'order_level' => 2,
            ],
            [
                'name' => ['ar' => 'قيمة الإعانة الشهرية', 'en' => 'Monthly Allowance Value'],
                'type' => 'income',
                'data_type' => 'number',
                'is_required' => false,
                'order_level' => 1,
            ],



            // Expenses 
            [
                'name' => ['ar' => 'فواتير الكهرباء والماء', 'en' => 'Utilities'],
                'type' => 'expense',
                'data_type' => 'number',
                'is_required' => true,
                'order_level' => 9,
            ],
            [
                'name' => ['ar' => 'الطعام والشراب', 'en' => 'Food and Beverages'],
                'type' => 'expense',
                'data_type' => 'number',
                'is_required' => true,
                'order_level' => 8,
            ],
            [
                'name' => ['ar' => 'النقل والمواصلات', 'en' => 'Transportation'],
                'type' => 'expense',
                'data_type' => 'number',
                'is_required' => true,
                'order_level' => 7,
            ],
            [
                'name' => ['ar' => 'الرعاية الصحية', 'en' => 'Healthcare'],
                'type' => 'expense',
                'data_type' => 'number',
                'is_required' => false,
                'order_level' => 6,
            ],
            [
                'name' => ['ar' => 'التعليم', 'en' => 'Education'],
                'type' => 'expense',
                'data_type' => 'number',
                'is_required' => false,
                'order_level' => 5,
            ],
            [
                'name' => ['ar' => 'الملابس', 'en' => 'Clothing'],
                'type' => 'expense',
                'data_type' => 'number',
                'is_required' => false,
                'order_level' => 4,
            ],
            [
                'name' => ['ar' => 'قروض وديون', 'en' => 'Loans and Debts'],
                'type' => 'expense',
                'data_type' => 'number',
                'is_required' => false,
                'order_level' => 3,
            ], 
        ];

        foreach ($economicStatuses as $economicStatus) {
            EconomicStatus::create($economicStatus);
        }
    }
} 