<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInterests extends Model
{
    protected $fillable = ["user_id", "age_min", "age_max", "gender"];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
