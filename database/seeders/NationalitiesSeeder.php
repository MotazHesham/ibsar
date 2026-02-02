<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

class NationalitiesSeeder extends Seeder
{
    public function run()
    {
        $nationalities = [
            [
                'name' => ['ar' => 'سعودي', 'en' => 'Saudi'],
            ],
            [
                'name' => ['ar' => 'مصري', 'en' => 'Egyptian'], 
            ],
            [
                'name' => ['ar' => 'سوداني', 'en' => 'Sudanese'],
            ],
            [
                'name' => ['ar' => 'يمني', 'en' => 'Yemeni'],
            ],
            [
                'name' => ['ar' => 'سوري', 'en' => 'Syrian'],
            ],
            [
                'name' => ['ar' => 'أردني', 'en' => 'Jordanian'],
            ],
            [
                'name' => ['ar' => 'فلسطيني', 'en' => 'Palestinian'],
            ],
            [
                'name' => ['ar' => 'لبناني', 'en' => 'Lebanese'],
            ],
            [
                'name' => ['ar' => 'عراقي', 'en' => 'Iraqi'],
            ],
            [
                'name' => ['ar' => 'إماراتي', 'en' => 'Emirati'],
            ],
            [
                'name' => ['ar' => 'كويتي', 'en' => 'Kuwaiti'],
            ],
            [
                'name' => ['ar' => 'بحريني', 'en' => 'Bahraini'],
            ],
            [
                'name' => ['ar' => 'عماني', 'en' => 'Omani'],
            ],
            [
                'name' => ['ar' => 'قطري', 'en' => 'Qatari'],
            ],
            [
                'name' => ['ar' => 'تونسي', 'en' => 'Tunisian'],
            ],
            [
                'name' => ['ar' => 'جزائري', 'en' => 'Algerian'],
            ],
            [
                'name' => ['ar' => 'مغربي', 'en' => 'Moroccan'],
            ],
            [
                'name' => ['ar' => 'ليبي', 'en' => 'Libyan'],
            ],
            [
                'name' => ['ar' => 'موريتاني', 'en' => 'Mauritanian'],
            ],
            [
                'name' => ['ar' => 'صومالي', 'en' => 'Somali'],
            ],
            [
                'name' => ['ar' => 'جيبوتي', 'en' => 'Djiboutian'],
            ],
            [
                'name' => ['ar' => 'تركي', 'en' => 'Turkish'],
            ],
            [
                'name' => ['ar' => 'إيراني', 'en' => 'Iranian'],
            ],
            [
                'name' => ['ar' => 'باكستاني', 'en' => 'Pakistani'],
            ],
            [
                'name' => ['ar' => 'هندي', 'en' => 'Indian'],
            ],
            // Asian Countries
            [
                'name' => ['ar' => 'صيني', 'en' => 'Chinese'],
            ],
            [
                'name' => ['ar' => 'ياباني', 'en' => 'Japanese'],
            ],
            [
                'name' => ['ar' => 'كوري', 'en' => 'Korean'],
            ],
            [
                'name' => ['ar' => 'تايلندي', 'en' => 'Thai'],
            ],
            [
                'name' => ['ar' => 'فيتنامي', 'en' => 'Vietnamese'],
            ],
            [
                'name' => ['ar' => 'فلبيني', 'en' => 'Filipino'],
            ],
            [
                'name' => ['ar' => 'إندونيسي', 'en' => 'Indonesian'],
            ],
            [
                'name' => ['ar' => 'ماليزي', 'en' => 'Malaysian'],
            ],
            [
                'name' => ['ar' => 'سنغافوري', 'en' => 'Singaporean'],
            ],
            [
                'name' => ['ar' => 'بنغلاديشي', 'en' => 'Bangladeshi'],
            ],
            [
                'name' => ['ar' => 'سريلانكي', 'en' => 'Sri Lankan'],
            ],
            [
                'name' => ['ar' => 'أفغاني', 'en' => 'Afghan'],
            ],
            // European Countries
            [
                'name' => ['ar' => 'بريطاني', 'en' => 'British'],
            ],
            [
                'name' => ['ar' => 'ألماني', 'en' => 'German'],
            ],
            [
                'name' => ['ar' => 'فرنسي', 'en' => 'French'],
            ],
            [
                'name' => ['ar' => 'إيطالي', 'en' => 'Italian'],
            ],
            [
                'name' => ['ar' => 'إسباني', 'en' => 'Spanish'],
            ],
            [
                'name' => ['ar' => 'روسي', 'en' => 'Russian'],
            ],
            [
                'name' => ['ar' => 'هولندي', 'en' => 'Dutch'],
            ],
            [
                'name' => ['ar' => 'بلجيكي', 'en' => 'Belgian'],
            ],
            [
                'name' => ['ar' => 'سويسري', 'en' => 'Swiss'],
            ],
            [
                'name' => ['ar' => 'نمساوي', 'en' => 'Austrian'],
            ],
            [
                'name' => ['ar' => 'سويدي', 'en' => 'Swedish'],
            ],
            [
                'name' => ['ar' => 'نرويجي', 'en' => 'Norwegian'],
            ],
            [
                'name' => ['ar' => 'دنماركي', 'en' => 'Danish'],
            ],
            [
                'name' => ['ar' => 'فنلندي', 'en' => 'Finnish'],
            ],
            [
                'name' => ['ar' => 'بولندي', 'en' => 'Polish'],
            ],
            [
                'name' => ['ar' => 'تشيكي', 'en' => 'Czech'],
            ],
            [
                'name' => ['ar' => 'يوناني', 'en' => 'Greek'],
            ],
            [
                'name' => ['ar' => 'برتغالي', 'en' => 'Portuguese'],
            ],
            // American Countries
            [
                'name' => ['ar' => 'أمريكي', 'en' => 'American'],
            ],
            [
                'name' => ['ar' => 'كندي', 'en' => 'Canadian'],
            ],
            [
                'name' => ['ar' => 'مكسيكي', 'en' => 'Mexican'],
            ],
            [
                'name' => ['ar' => 'برازيلي', 'en' => 'Brazilian'],
            ],
            [
                'name' => ['ar' => 'أرجنتيني', 'en' => 'Argentine'],
            ],
            [
                'name' => ['ar' => 'تشيلي', 'en' => 'Chilean'],
            ],
            [
                'name' => ['ar' => 'بيروفي', 'en' => 'Peruvian'],
            ],
            [
                'name' => ['ar' => 'كولومبي', 'en' => 'Colombian'],
            ],
            [
                'name' => ['ar' => 'فنزويلي', 'en' => 'Venezuelan'],
            ],
            // African Countries
            [
                'name' => ['ar' => 'نيجيري', 'en' => 'Nigerian'],
            ],
            [
                'name' => ['ar' => 'جنوب أفريقي', 'en' => 'South African'],
            ],
            [
                'name' => ['ar' => 'كيني', 'en' => 'Kenyan'],
            ],
            [
                'name' => ['ar' => 'إثيوبي', 'en' => 'Ethiopian'],
            ],
            [
                'name' => ['ar' => 'غاني', 'en' => 'Ghanaian'],
            ],
            [
                'name' => ['ar' => 'أوغندي', 'en' => 'Ugandan'],
            ],
            [
                'name' => ['ar' => 'تنزاني', 'en' => 'Tanzanian'],
            ],
            [
                'name' => ['ar' => 'زيمبابوي', 'en' => 'Zimbabwean'],
            ],
            // Oceania
            [
                'name' => ['ar' => 'أسترالي', 'en' => 'Australian'],
            ],
            [
                'name' => ['ar' => 'نيوزيلندي', 'en' => 'New Zealander'],
            ]
        ];

        foreach ($nationalities as $nationality) {
            \App\Models\Nationality::create($nationality);
        }
    }
}
