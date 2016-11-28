<?php

namespace App\Models;

class UserInterests extends Model
{
    protected $fillable = ["user_id", "age_min", "age_max", "gender"];

    public $id = 0;
    public $age_min = 0;
    public $age_max = 0;
    public $gender = 0;
    public $created_at = null;
    public $updated_at = null;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function tableDefinition()
    {
        return new \CreateUserInterestsTable();
    }
}
