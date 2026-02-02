<?php

namespace Database\Seeders;

use App\Models\RequiredDocument;
use Illuminate\Database\Seeder;

class RequiredDocumentSeeder extends Seeder
{
    public function run()
    {
        $requiredDocuments = [
            [
                'name' => ['ar' => 'الهوية الوطنية', 'en' => 'National ID'], 
                'is_required' => true, 
            ],
            [
                'name' => ['ar' => 'شهادة الميلاد', 'en' => 'Birth Certificate'],
            ],
            [
                'name' => ['ar' => 'شهادة الزواج', 'en' => 'Marriage Certificate'],
            ],
            [
                'name' => ['ar' => 'شهادة الطلاق', 'en' => 'Divorce Certificate'],
            ],
            [
                'name' => ['ar' => 'شهادة الوفاة', 'en' => 'Death Certificate'],
            ],
        ];

        foreach ($requiredDocuments as $requiredDocument) {
            RequiredDocument::create($requiredDocument);
        }
    }
} 