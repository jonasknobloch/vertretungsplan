<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 18.08.17
 * Time: 15:27
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manager extends Model
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
        'pivot'
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

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function schools() {
        return $this->belongsToMany(School::class, 'manager_school', 'manager_user_id', 'school_id')->withTimestamps();
    }

    public function changes() {
        return $this->hasMany(Change::class, 'manager_user_id', 'user_id');
    }

    public function schedule() {
        return $this->hasOne(Schedule::class, 'manager_user_id', 'user_id');
    }
}