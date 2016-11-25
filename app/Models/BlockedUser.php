<?php

namespace App\Models;

use App\Models\Model;
class BlockedUser extends Model
{
    protected $fillables = ['object_id','subject_id'];

    public function blockedBy()
    {
        return $this->belongsTo('App\User','object_id');
    }
    public function blockedUser()
    {
        return $this->belongsTo('App\User','subject_id');
    }
    public function tableDefinition()
    {
        return new \CreateBlockedUsersTable();
    }
}
