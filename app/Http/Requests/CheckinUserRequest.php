<?php

namespace Requests;

use Requests\Request;

class CheckinUserRequest extends Request
{

    public function __construct(){
        $this->authenticatable = true;
    }

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
            'lat' => 'required',
            'long' => 'required'
        ];
    }
}
