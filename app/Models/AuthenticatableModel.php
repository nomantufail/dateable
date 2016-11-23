<?php

namespace App\Models;

use App\Traits\Authenticatable;
use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class AuthenticatableModel extends Model
{
    use Authenticatable;

    public function setRawAttributes(array $attributes, $sync = false)
    {
        foreach($attributes as $key=>$value){
            if(isset($this->$key)){
                $this->$key = $value;
            }
        }
        $this->attributes = $attributes;
        if ($sync) {
            $this->syncOriginal();
        }
        return $this;
    }
}
