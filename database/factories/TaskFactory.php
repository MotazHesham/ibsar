<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    private array $tasks = [
        // شراء مستلزمات (Purchasing Supplies)
        [
            'name' => 'شراء مستلزمات المكتب',
            'short_description' => 'شراء الأقلام والدفاتر والمستلزمات المكتبية الأساسية',
            'description' => 'تحديد قائمة المستلزمات المكتبية المطلوبة وتقديم عروض الأسعار من ثلاثة موردين مختلفين وشراء الكمية المطلوبة مع التأكد من جودة المنتجات',
            'task_board_id' => 1
        ],
        [
            'name' => 'شراء أثاث جديد',
            'short_description' => 'تحديث أثاث المكتب الرئيسي',
            'description' => 'بحث عن أثاث مكتبي جديد يتناسب مع المساحة المتوفرة وتقديم مقترحات للتصميم مع مراعاة الميزانية المحددة',
            'task_board_id' => 1
        ],
        [
            'name' => 'شراء معدات تقنية',
            'short_description' => 'تحديث أجهزة الكمبيوتر والطابعات',
            'description' => 'تقييم احتياجات الموظفين من الأجهزة التقنية وشراء أجهزة كمبيوتر جديدة وطابعات مع ضمانات مناسبة',
            'task_board_id' => 1
        ],
        [
            'name' => 'شراء مستلزمات المطبخ',
            'short_description' => 'توفير مستلزمات المطبخ الصغير',
            'description' => 'شراء ماكينة قهوة وأدوات مطبخ أساسية ومستلزمات المطبخ اليومية للموظفين',
            'task_board_id' => 1
        ],
        [
            'name' => 'شراء مواد تنظيف',
            'short_description' => 'توفير مواد التنظيف للمكتب',
            'description' => 'شراء مواد تنظيف متنوعة وتنظيم مخزون مواد التنظيف وتحديد مواعيد إعادة الطلب',
            'task_board_id' => 1
        ],

        // تصميم الموقع (Website Design)
        [
            'name' => 'تصميم الصفحة الرئيسية',
            'short_description' => 'إنشاء تصميم جذاب للصفحة الرئيسية',
            'description' => 'تصميم واجهة الصفحة الرئيسية مع التركيز على تجربة المستخدم وسهولة التنقل وجاذبية التصميم',
            'task_board_id' => 2
        ],
        [
            'name' => 'تصميم نظام الألوان',
            'short_description' => 'تحديد نظام الألوان الرئيسي للموقع',
            'description' => 'اختيار نظام ألوان متناسق يعكس هوية الشركة مع مراعاة معايير الوصول وسهولة القراءة',
            'task_board_id' => 2
        ],
        [
            'name' => 'تصميم صفحات المنتجات',
            'short_description' => 'تصميم صفحات عرض المنتجات',
            'description' => 'تصميم قوالب صفحات المنتجات مع التركيز على عرض المعلومات المهمة والصور بشكل جذاب',
            'task_board_id' => 2
        ],
        [
            'name' => 'تصميم نموذج الاتصال',
            'short_description' => 'إنشاء نموذج اتصال سهل الاستخدام',
            'description' => 'تصميم نموذج اتصال يتضمن جميع الحقول المطلوبة مع التحقق من صحة المدخلات',
            'task_board_id' => 2
        ],
        [
            'name' => 'تصميم القائمة الرئيسية',
            'short_description' => 'تصميم قائمة التنقل الرئيسية',
            'description' => 'تصميم قائمة تنقل سهلة الاستخدام تعمل بشكل جيد على جميع الأجهزة',
            'task_board_id' => 2
        ],

        // كتابة المحتوى (Content Writing)
        [
            'name' => 'كتابة وصف الشركة',
            'short_description' => 'كتابة نبذة تعريفية عن الشركة',
            'description' => 'كتابة محتوى جذاب يشرح تاريخ الشركة ورؤيتها وقيمها وخدماتها بشكل احترافي',
            'task_board_id' => 3
        ],
        [
            'name' => 'كتابة وصف المنتجات',
            'short_description' => 'كتابة أوصاف تفصيلية للمنتجات',
            'description' => 'كتابة أوصاف احترافية للمنتجات مع التركيز على المميزات والفوائد واستخدام كلمات مفتاحية مناسبة',
            'task_board_id' => 3
        ],
        [
            'name' => 'كتابة سياسة الخصوصية',
            'short_description' => 'صياغة سياسة الخصوصية للموقع',
            'description' => 'كتابة سياسة خصوصية شاملة وواضحة تتوافق مع متطلبات حماية البيانات',
            'task_board_id' => 3
        ],
        [
            'name' => 'كتابة الشروط والأحكام',
            'short_description' => 'صياغة شروط وأحكام استخدام الموقع',
            'description' => 'كتابة شروط وأحكام واضحة ومفصلة تغطي جميع جوانب استخدام الموقع',
            'task_board_id' => 3
        ],
        [
            'name' => 'كتابة الأسئلة الشائعة',
            'short_description' => 'إعداد قسم الأسئلة الشائعة',
            'description' => 'كتابة إجابات شاملة للأسئلة المتكررة مع تنظيمها بشكل منطقي وسهل القراءة',
            'task_board_id' => 3
        ],

        // دعم العملاء (Customer Support)
        [
            'name' => 'معالجة شكوى العميل',
            'short_description' => 'الرد على شكوى عميل حول تأخر الشحن',
            'description' => 'التواصل مع العميل وتتبع حالة الشحنة وتقديم حل مناسب مع ضمان رضا العميل',
            'task_board_id' => 4
        ],
        [
            'name' => 'تحديث دليل الدعم الفني',
            'short_description' => 'تحديث إجراءات الدعم الفني',
            'description' => 'مراجعة وتحديث إجراءات الدعم الفني وإضافة حلول للمشاكل الشائعة',
            'task_board_id' => 4
        ],
        [
            'name' => 'تدريب فريق الدعم',
            'short_description' => 'إجراء جلسة تدريبية لفريق الدعم',
            'description' => 'إعداد وتنفيذ جلسة تدريبية حول التعامل مع العملاء وحل المشاكل المعقدة',
            'task_board_id' => 4
        ],
        [
            'name' => 'تحليل رضا العملاء',
            'short_description' => 'تحليل نتائج استبيان رضا العملاء',
            'description' => 'تحليل نتائج استبيان رضا العملاء وتقديم توصيات لتحسين الخدمة',
            'task_board_id' => 4
        ],
        [
            'name' => 'تحديث نظام التذاكر',
            'short_description' => 'تحديث نظام إدارة تذاكر الدعم',
            'description' => 'تحديث نظام إدارة تذاكر الدعم وإضافة ميزات جديدة لتحسين كفاءة العمل',
            'task_board_id' => 4
        ],

        // إدارة المشاريع (Project Management)
        [
            'name' => 'تحديث خطة المشروع',
            'short_description' => 'تحديث خطة المشروع الحالي',
            'description' => 'مراجعة وتحديث خطة المشروع مع فريق العمل وتعديل الجدول الزمني حسب التقدم',
            'task_board_id' => 5
        ],
        [
            'name' => 'اجتماع تنسيق المشروع',
            'short_description' => 'عقد اجتماع تنسيق أسبوعي',
            'description' => 'عقد اجتماع تنسيق أسبوعي مع فريق المشروع لمناقشة التقدم والتحديات',
            'task_board_id' => 5
        ],
        [
            'name' => 'تقرير حالة المشروع',
            'short_description' => 'إعداد تقرير حالة المشروع الشهري',
            'description' => 'إعداد تقرير مفصل عن حالة المشروع يتضمن التقدم والميزانية والمخاطر',
            'task_board_id' => 5
        ],
        [
            'name' => 'تحديث موارد المشروع',
            'short_description' => 'تحديث خطة موارد المشروع',
            'description' => 'مراجعة وتحديث خطة موارد المشروع وتوزيع المهام على الفريق',
            'task_board_id' => 5
        ],
        [
            'name' => 'إدارة مخاطر المشروع',
            'short_description' => 'تحديث سجل مخاطر المشروع',
            'description' => 'تحديث سجل مخاطر المشروع وتطوير خطط التخفيف للتعامل مع المخاطر الجديدة',
            'task_board_id' => 5
        ],

        // الموارد البشرية (Human Resources)
        [
            'name' => 'توظيف مطور جديد',
            'short_description' => 'إدارة عملية توظيف مطور برمجيات',
            'description' => 'إدارة عملية التوظيف من نشر الإعلان إلى إجراء المقابلات واختيار المرشح المناسب',
            'task_board_id' => 6
        ],
        [
            'name' => 'تقييم أداء الموظفين',
            'short_description' => 'إجراء تقييمات أداء ربع سنوية',
            'description' => 'إجراء تقييمات أداء للموظفين وتقديم التغذية الراجعة وتحديد أهداف جديدة',
            'task_board_id' => 6
        ],
        [
            'name' => 'تدريب الموظفين',
            'short_description' => 'تنظيم برنامج تدريبي للموظفين',
            'description' => 'تنظيم وتنفيذ برنامج تدريبي لتحسين مهارات الموظفين في مجالات محددة',
            'task_board_id' => 6
        ],
        [
            'name' => 'تحديث سياسات الشركة',
            'short_description' => 'مراجعة وتحديث سياسات الموارد البشرية',
            'description' => 'مراجعة وتحديث سياسات الموارد البشرية وتوضيحها للموظفين',
            'task_board_id' => 6
        ],
        [
            'name' => 'تخطيط المزايا والتعويضات',
            'short_description' => 'تحديث خطة المزايا والتعويضات',
            'description' => 'مراجعة وتحديث خطة المزايا والتعويضات للموظفين مع مراعاة الميزانية',
            'task_board_id' => 6
        ]
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('ar_SA');
        $task = $faker->randomElement($this->tasks);
        
        return [
            'name' => $task['name'],
            'short_description' => $task['short_description'],
            'description' => $task['description'],
            'status_id' => $faker->numberBetween(1, 3),
            'task_priority_id' => $faker->numberBetween(1, 3),
            'due_date' => $faker->dateTimeBetween('now', '+1 year')->format(config('panel.date_format')),
            'task_board_id' => $task['task_board_id'],
            'assigned_by_id' => $faker->numberBetween(1, 40),
        ];
    }
}
