<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*class Student extends Model
{

    protected $appends = [
        'schools', 'classes'
    ];

    public function setSchoolsAttribute($value)
    {
        $this->attributes['schools'] = $value;
    }

    public function getSchoolsAttribute() {
        return $this->attributes['schools'];
    }

    public function setClassesAttribute($value)
    {
        $this->attributes['classes'] = $value;
    }

    public function getClassesAttribute() {
        return $this->attributes['classes'];
    }
}*/

class Student extends Model
{
    protected $primaryKey = 'user_id';
    protected $keyType = 'string';
    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
    ];

    protected $appends = [
        'first_name', 'last_name', 'email',
    ];

    protected $hidden = [

    ];

    public function getFirstNameAttribute() {
        return $this->user()->first()->first_name;
    }

    public function getLastNameAttribute() {
        return $this->user()->first()->last_name;
    }

    public function getEmailAttribute() {
        return $this->user()->first()->email;
    }

    public function setClassesAttribute($value)
    {
        $this->attributes['classes'] = $value;
    }

    public function getClassesAttribute() {
        return $this->attributes['classes'];
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function schools() {
        return $this->belongsToMany(School::class, 'school_student', 'student_user_id', 'school_id')->withPivot('class')->withTimestamps();
    }
}
