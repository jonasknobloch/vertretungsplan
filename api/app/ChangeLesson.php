<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 03.05.17
 * Time: 15:17
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChangeLesson extends Model {
    protected $table = 'changes_lessons';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'change_id', 'class', 'index', 'subject', 'teacher', 'room', 'info', 'group',
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