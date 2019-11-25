<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'first_name', 'last_name', 'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [
        'role'
    ];

    public function setRoleAttribute($value)
    {
        $this->attributes['role'] = $value;
    }

    public function getRoleAttribute()
    {
        if (!isset($this->attributes['role'])) {
            $isAdmin = $this->admin()->get()->isNotEmpty();
            $isManager = $this->manager()->get()->isNotEmpty();
            $isStudent = $this->student()->get()->isNotEmpty();

            return $isAdmin ? 'admin' : ($isManager ? 'manager' : ($isStudent ? 'student' : 'staff'));
        } else {
            return $this->attributes['role'];
        }
    }

    public function setStudentAttribute($student) {
        $this->attributes['student'] = $student;
    }

    public function admin() {
        return $this->hasOne(Admin::class);
    }

    public function manager() {
        return $this->hasOne(Manager::class);
    }

    public function student() {
        return $this->hasOne(Student::class);
    }

    public function authToken() {
        return $this->hasOne(UserAuthToken::class);
    }
}
