<?php

namespace Requests\Foo;

use Requests\Request;

class FooRequest extends Request
{
    public function __construct(){
        $this->authenticatable = true;
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //'userIId'=>'required'
        ];
    }
}
