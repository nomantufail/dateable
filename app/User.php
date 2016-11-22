<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    public $id = 0;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function blockedUsers()
    {
        return $this->hasMany('App\Models\BlockedUser','object_id');
    }
    public function likedUsers()
    {
        return $this->hasMany('App\Models\LikedUser','object_id');
    }
    public function likedBy()
    {
        return $this->hasMany('App\Models\LikedUser','subject_id');
    }
    public function blockedBy()
    {
        return $this->hasMany('App\Models\BlockedUser','subject_id');
    }
    public function interests()
    {
        return $this->hasOne('App\Models\UserInterest');
    }

    public function checkedIns()
    {
        return $this->hasMany('App\Models\CheckedIn');
    }

    public function setRawAttributes(array $attributes, $sync = false)
    {
        $this->id = $attributes['id'];
        $this->attributes = $attributes;
        if ($sync) {
            $this->syncOriginal();
        }
        return $this;
    }
}
