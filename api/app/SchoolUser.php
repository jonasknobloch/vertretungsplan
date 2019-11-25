<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 03.05.17
 * Time: 15:16
 */

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SchoolUser extends Pivot {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'school_id', 'user_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_user_id', 'user_id');
    }
}