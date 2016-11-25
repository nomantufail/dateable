<?php

namespace Requests;

use App\User;
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
            //
        ];
    }

    public function getFbUser()
    {
        $user = new User();
        $user->fb_id = $this->get('fb_id');
        $user->email = $this->get('email');
        $user->name = $this->get('name');
        $user->password = "";
        $user->remember_token = "";
        $user->access_token = "";
        return $user;
    }
}
