<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 18.08.17
 * Time: 15:18
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model {

    protected $primaryKey = 'school_id';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'school_id', 'manager_user_id'
    ];

    public function school() {
        return $this->belongsTo(School::class);
    }

    public function manager() {
        return $this->belongsTo(Manager::class, 'manager_user_id', 'user_id');
    }

    public function lessons() {
        return $this->hasMany(ScheduleLesson::class);
    }
}