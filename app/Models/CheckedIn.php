<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckedIn extends Model
{
    protected $fillable = ["user_id","checked_in","checked_out","lat","long"];

    public function user()
    {
        $this->belongsTo('App\User');
    }
}
