<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentLesson extends Model
{
    protected $table = 'students_lessons';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'student_user_id', 'school_id', 'subject', 'teacher','group',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}