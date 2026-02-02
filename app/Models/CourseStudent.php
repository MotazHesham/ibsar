<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Utils\LogsModelActivity;
class CourseStudent extends Model
{
    use SoftDeletes, HasFactory;
    use LogsModelActivity;
    public $table = 'course_students';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'beneficiary_order_id',
        'course_id',
        'beneficiary_id',
        'beneficiary_family_id',
        'note',
        'certificate',
        'transportation',
        'prev_experience',
        'prev_courses',
        'attend_same_course_before',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }

    public function beneficiary_family()
    {
        return $this->belongsTo(BeneficiaryFamily::class, 'beneficiary_family_id');
    }

    public function beneficiaryOrder()
    {
        return $this->belongsTo(BeneficiaryOrder::class, 'beneficiary_order_id');
    }   

    public function attendance()
    {
        return $this->hasMany(CourseAttendance::class, 'course_student_id');
    }
}
