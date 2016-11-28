<?php

namespace App\Models;


class CheckedIn extends Model
{
    protected $fillable = ["user_id","checked_in","checked_out","lat","long"];

    public $user_id = 0;
    public $lat = 0;
    public $long = 0;
    public $checked_in = "";

    public function user()
    {
        $this->belongsTo('App\User');
    }

    public function tableDefinition()
    {
        return new \CreateCheckedInsTable();
    }
}
