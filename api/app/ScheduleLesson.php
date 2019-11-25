<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 27.04.17
 * Time: 23:23
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleLesson extends Model {
    protected $table = 'schedules_lessons';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'schedule_school_id', 'day', 'class', 'index', 'week', 'subject', 'teacher', 'room', 'group', 'coupling',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_school_id', 'school_id');
    }

    public function setChangeAttribute($lesson)
    {
        $this->attributes['change'] = $lesson;
    }

    public function getChangeAttribute() {
        return $this->attributes['change'];
    }
}