<?php

namespace App;

use App\Models\AuthenticatableModel;
use Illuminate\Notifications\Notifiable;

class User extends AuthenticatableModel
{
    use Notifiable;

    public $id = 0;
    public $first_name = "";
    public $last_name = "";
    public $email = "";
    public $about = "";
    public $password = "";
    public $fb_id = 0;
    public $access_token = "";
    public $remember_token = "";
    public $gender = 0;
    public $birthday = "";
    public $active = 1;

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

    public function blockedUsers()
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
        return $this->hasOne('App\Models\UserInterests');
    }
    public function checkedIns()
    {
        return $this->hasMany('App\Models\CheckedIn');
    }

    public function tableDefinition()
    {
        return new \CreateUsersTable();
    }
}
