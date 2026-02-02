<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class CourseAttendance extends Model
{
    use HasFactory;
    public $table = 'course_attendances';

    protected $dates = [
        'date',
        'created_at',
        'updated_at', 
    ];

    protected $fillable = [
        'date',
        'course_student_id',
        'course_id', 
        'created_at',
        'updated_at',
    ];
    

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function courseStudent()
    {
        return $this->belongsTo(CourseStudent::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
