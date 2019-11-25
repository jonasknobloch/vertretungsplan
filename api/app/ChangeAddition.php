<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 03.05.17
 * Time: 15:16
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChangeAddition extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'change_id', 'title', 'content',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function change()
    {
        return $this->belongsTo(Change::class);
    }
}