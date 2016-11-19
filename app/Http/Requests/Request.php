<?php
/**
 * Created by PhpStorm.
 * User: nomantufail
 * Date: 11/16/2016
 * Time: 10:50 AM
 */

namespace Requests;


abstract class Request
{

    public $authenticateable = false;
    protected $autoTransform = false;

    public abstract function authorize();
    public abstract function rules();

    public function transform()
    {
        return [];
    }
    private function transformAutomatically()
    {
        $transformedValues = [];
        collect($this->originalRequest()->all())->each(function($value, $key) use(&$transformedValues){
            $transformedValues[lcFirst(str_replace(' ','', ucwords(join(' ', explode('_',$key)))))] = $value;
        })->toArray();
        return $transformedValues;
    }

    public function messages()
    {
        return [];
    }

    public function originalRequest()
    {
        return request();
    }

    /**
     * @return array
     */
    public function all(){
        $inputs = $this->autoTransform ? $this->transformAutomatically() : $this->originalRequest()->all();
        foreach($this->transform() as $key=>$value){
            $inputs[$key] = $value;
        }
        return $inputs;
    }

    public function get($key){
        return (!isset($this->all()[$key]))?$this->originalRequest()->get($key):$this->all()[$key];
    }

    public function input($key){
        return (!isset($this->all()[$key]))?$this->originalRequest()->input($key):$this->all()[$key];
    }

    public function file($key){
        return $this->originalRequest()->file($key);
    }

}