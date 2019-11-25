<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 27.04.17
 * Time: 23:23
 */

namespace App;

use Illuminate\Database\Eloquent\Model;


use Illuminate\Contracts\Auth\Authenticatable;

use Illuminate\Auth\Authenticatable as AuthenticableTrait;

class School extends Model implements Authenticatable {

    use AuthenticableTrait;

    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'api_token'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'pivot', 'api_token'
    ];

    protected $casts = [
        'id' => 'integer'
    ];

    public function schedule()
    {
        return $this->hasOne(Schedule::class);
    }

    public function changes()
    {
        return $this->hasMany(Change::class);
    }

    public function students() {
        return $this->belongsToMany(Student::class, 'school_student', 'school_id', 'student_user_id')->withPivot('class')->withTimestamps();
    }

    public function managers() {
        return $this->belongsToMany(Manager::class, 'manager_school', 'school_id', 'manager_user_id')->withTimestamps();
    }
}