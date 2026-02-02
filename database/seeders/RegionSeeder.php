<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $RegionsWithCities = [
            "الحدود الشمالية" => [ "طريف", "عرعر", "رفحاء" ],
            "الجوف" => [ "القريات", "سكاكا", "دومة الجندل" ],
            "تبوك" => [ "تبوك", "الوجه", "املج", "ضبا" ],
            "حائل" => [ "حائل", "الشنان" ],
            "القصيم" => [ "بريدة", "عنيزة", "الرس", "المذنب", "رياض الخبراء", "البدائع", "البكيرية", "الشماسية" ],
            "الرياض" => [ "الرياض", "المجمعة", "الزلفي", "الغاط", "عفيف", "ثادق", "شقراء", "الدوادمي", "الدرعية", "القويعية", "المزاحمية", "الخرج", "الدلم", "السليل", "الحريق", "حوطة بني تميم", "ليلى" ],
            "المدينة المنورة" => [ "المدينة المنورة", "خيبر", "ينبع", "بدر" ],
            "عسير" => [ "ابها", "خميس مشيط", "احد رفيده", "المجاردة", "تثليث", "بيشة", "محايل", "بللسمر" ],
            "الباحة" => [ "الباحة" ],
            "جازان" => [ "جازان", "صبيا", "ابو عريش", "صامطة", "احد المسارحة" ],
            "مكة المكرمة" => [ "الطائف", "مكة المكرمة", "جدة", "رابغ", "الخرمة", "الجموم", "القنفذة", "ثول", "تربه", "مدينة الملك عبدالله الاقتصادية" ],
            "نجران" => [ "شرورة", "نجران" ],
            "الشرقية" => [ "الهفوف", "الدمام", "الخبر", "حفر الباطن", "القطيف", "قرية العليا", "الجبيل", "النعيرية", "الظهران", "بقيق", "سيهات", "تاروت", "صفوى", "عنك", "الخفجي", "رأس تنورة" ]
        ];
        
        foreach ($RegionsWithCities as $regionArName => $citiesArNames) {
            // Create the region with translations
            $region = Region::firstOrCreate(
                ['name->ar' => $regionArName],
                ['name' => ['ar' => $regionArName, 'en' => $this->transliterateRegion($regionArName)]]
            );
        
            foreach ($citiesArNames as $citiesArName) {
                // Check if the district already exists and is associated with the city
                $city = City::where('name->ar', $citiesArName)
                    ->orWhere('name->en', $this->transliterateCity($citiesArName))
                    ->first();
            
                if (!$city) {
                    // Create the city if it doesn't exist
                    $city = City::create([
                        'name' => ['ar' => $citiesArName, 'en' => $this->transliterateCity($citiesArName)],
                    ]);
                }
            
                // Attach the city to the city if not already attached
                $region->cities()->syncWithoutDetaching([$city->id]);
            }
        }
    }

    private function transliterateRegion($arabicName){
        $translations = [
            'الحدود الشمالية' => 'Northern Frontier',
            'الجوف' => 'Al Jawf',
            'تبوك' => 'Tabuk',
            'حائل' => 'Hail',
            'القصيم' => 'Al Qassim',
            'الرياض' => 'Ar Riyadh',
            'المدينة المنورة' => 'Al Madinah Al Munawwarah',
            'عسير' => 'Asir',
            'الباحة' => 'Al Baha',
            'جازان' => 'Jazan',
            'مكة المكرمة' => 'Makkah Al Mukarramah',
            'نجران' => 'Najran',
            'الشرقية' => 'Eastern',
        ];
    
        return $translations[$arabicName] ?? $arabicName;
    }
    private function transliterateCity($arabicName)
    {
        $translations = [
            'تبوك' => 'Tabuk',
            'الرياض' => 'Riyadh',
            'الطائف' => 'At Taif',
            'مكة المكرمة' => 'Makkah Al Mukarramah',
            'حائل' => 'Hail',
            'بريدة' => 'Buraydah',
            'الهفوف' => 'Al Hufuf',
            'الدمام' => 'Ad Dammam',
            'المدينة المنورة' => 'Al Madinah Al Munawwarah',
            'ابها' => 'Abha',
            'جازان' => 'Jazan',
            'جدة' => 'Jeddah',
            'المجمعة' => 'Al Majmaah',
            'الخبر' => 'Al Khubar',
            'حفر الباطن' => 'Hafar Al Batin',
            'خميس مشيط' => 'Khamis Mushayt',
            'احد رفيده' => 'Ahad Rifaydah',
            'القطيف' => 'Al Qatif',
            'عنيزة' => 'Unayzah',
            'قرية العليا' => 'Qaryat Al Ulya',
            'الجبيل' => 'Al Jubail',
            'النعيرية' => 'An Nuayriyah',
            'الظهران' => 'Dhahran',
            'الوجه' => 'Al Wajh',
            'بقيق' => 'Buqayq',
            'الزلفي' => 'Az Zulfi',
            'خيبر' => 'Khaybar',
            'الغاط' => 'Al Ghat',
            'املج' => 'Umluj',
            'رابغ' => 'Rabigh',
            'عفيف' => 'Afif',
            'ثادق' => 'Thadiq',
            'سيهات' => 'Sayhat',
            'تاروت' => 'Tarut',
            'ينبع' => 'Yanbu',
            'شقراء' => 'Shaqra',
            'الدوادمي' => 'Ad Duwadimi',
            'الدرعية' => 'Ad Diriyah',
            'القويعية' => 'Quwayiyah',
            'المزاحمية' => 'Al Muzahimiyah',
            'بدر' => 'Badr',
            'الخرج' => 'Al Kharj',
            'الدلم' => 'Ad Dilam',
            'الشنان' => 'Ash Shinan',
            'الخرمة' => 'Al Khurmah',
            'الجموم' => 'Al Jumum',
            'المجاردة' => 'Al Majardah',
            'السليل' => 'As Sulayyil',
            'تثليث' => 'Tathilith',
            'بيشة' => 'Bishah',
            'الباحة' => 'Al Baha',
            'القنفذة' => 'Al Qunfidhah',
            'محايل' => 'Muhayil',
            'ثول' => 'Thuwal',
            'ضبا' => 'Duba',
            'تربه' => 'Turbah',
            'صفوى' => 'Safwa',
            'عنك' => 'Inak',
            'طريف' => 'Turaif',
            'عرعر' => 'Arar',
            'القريات' => 'Al Qurayyat',
            'سكاكا' => 'Sakaka',
            'رفحاء' => 'Rafha',
            'دومة الجندل' => 'Dawmat Al Jandal',
            'الرس' => 'Ar Rass',
            'المذنب' => 'Al Midhnab',
            'الخفجي' => 'Al Khafji',
            'رياض الخبراء' => 'Riyad Al Khabra',
            'البدائع' => 'Al Badai',
            'رأس تنورة' => 'Ras Tannurah',
            'البكيرية' => 'Al Bukayriyah',
            'الشماسية' => 'Ash Shimasiyah',
            'الحريق' => 'Al Hariq',
            'حوطة بني تميم' => 'Hawtat Bani Tamim',
            'ليلى' => 'Layla',
            'بللسمر' => 'Billasmar',
            'شرورة' => 'Sharurah',
            'نجران' => 'Najran',
            'صبيا' => 'Sabya',
            'ابو عريش' => 'Abu Arish',
            'صامطة' => 'Samtah',
            'احد المسارحة' => 'Ahad Al Musarihah',
            'مدينة الملك عبدالله الاقتصادية' => 'King Abdullah Economic City'
        ];
    
        return $translations[$arabicName] ?? $arabicName;
    }
}
