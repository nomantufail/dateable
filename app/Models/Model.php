<?php

namespace App\Models;

use App\Traits\Authenticatable;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Migrations\Migration;

abstract class Model extends EloquentModel
{
    /**
     * @return Migration
     * */
    public abstract function tableDefinition();
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

    public function save(array $options = [])
    {
        foreach($this->fields() as $field){
            if(!isset($this->attributes[$field]) && $field != 'id')
                $this->attributes[$field] = $this->$field;
        }
        return parent::save();
    }

    public function fields()
    {
        return $this->tableDefinition()->fields();
    }
}
