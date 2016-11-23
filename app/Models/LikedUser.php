<?php

namespace App\Models;

class LikedUser extends Model
{
    protected $fillables = ['object_id','subject_id'];

    public function likedBy()
    {
        return $this->belongsTo('App\User','object_id');
    }
    public function likedUser()
    {
        return $this->belongsTo('App\User','subject_id');
    }
}
