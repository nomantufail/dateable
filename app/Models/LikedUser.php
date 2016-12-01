<?php

namespace App\Models;

class LikedUser extends Model
{
    public $id = 0;
    public $object_id = 0;
    public $subject_id = 0;
    public $updated_at = null;
    public $created_at = null;

    protected $fillables = ['object_id','subject_id'];

    public function likedBy()
    {
        return $this->belongsTo('App\User','object_id');
    }
    public function likedUser()
    {
        return $this->belongsTo('App\User','subject_id');
    }

    public function tableDefinition()
    {
        return new \CreateLikedUsersTable();
    }
}
