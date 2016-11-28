<?php

namespace App\Models;


class CheckedIn extends Model
{
    protected $fillable = ["user_id","checked_in","checked_out","lat","long"];

    public function user()
    {
        $this->belongsTo('App\User');
    }

    public function tableDefinition()
    {
        return new \CreateCheckedInsTable();
    }
}
