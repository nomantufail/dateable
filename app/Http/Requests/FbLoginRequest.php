<?php

namespace Requests;

use App\User;
use Carbon\Carbon;
use Requests\Request;

class FbLoginRequest extends Request
{

    public function __construct(){
        $this->authenticatable = false;
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
            'id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'gender' => 'required'
        ];
    }

    public function getFbUser()
    {
        $user = new User();
        $user->fb_id = $this->get('id');
        $user->first_name = $this->get('first_name');
        $user->last_name = $this->get('last_name');
        $user->email = $this->get('email');
        $user->gender = $this->get('gender');
        $user->birthday = Carbon::createFromFormat('m/d/Y',$this->get('birthday'))->toDateString();
        $user->password = "";
        $user->remember_token = "";
        $user->access_token = "";
        return $user;
    }
}
