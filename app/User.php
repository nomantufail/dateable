<?php

namespace App;

use App\Models\Model;
use App\Traits\Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use Notifiable, Authenticatable;

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
}
