<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 18.08.17
 * Time: 15:48
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
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
}