<?php
/**
 * Created by PhpStorm.
 * User: nomantufail
 * Date: 11/16/2016
 * Time: 10:50 AM
 */

namespace Requests;


use App\Libs\Auth\Auth;

abstract class Request
{
    public abstract function authorize();
    public abstract function rules();

    /**
     * This variable defines weather a request should be authenticated or not.
     * */
    public $authenticatable = true;

    public function user()
    {
        return Auth::user();
    }

    public function messages()
    {
        return [];
    }

    public function originalRequest()
    {
        return request();
    }

    public function all(){
        return $this->originalRequest()->all();
    }

    public function get($key){
        return $this->originalRequest()->get($key);
    }

    public function input($key){
        return $this->originalRequest()->input($key);
    }

    public function file($key){
        return $this->originalRequest()->file($key);
    }
}