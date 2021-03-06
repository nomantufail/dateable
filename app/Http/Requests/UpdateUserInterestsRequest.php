<?php

namespace Requests;

use Requests\Request;

class UpdateUserInterestsRequest extends Request
{

    public function __construct(){
        parent::__construct();
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
            'age_min' => 'required',
            'age_max' => 'required',
            'gender' => 'numeric|required|min:0|max:2',
        ];
    }
}
