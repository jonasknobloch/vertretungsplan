<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 03.05.17
 * Time: 15:16
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Change extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'school_id', 'manager_user_id', 'date', 'week',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function manager()
    {
        return $this->belongsTo(Manager::class, 'manager_user_id', 'user_id');
    }

    public function lessons()
    {
        return $this->hasMany(ChangeLesson::class);
    }

    public function additions()
    {
        return $this->hasMany(ChangeAddition::class);
    }
}