<?php

namespace Requests;

use App\Models\BlockedUser;
use Requests\Request;

class BlockUserRequest extends Request
{

    public function __construct(){
        parent::__construct();
    }

    public function blockedUserModel()
    {
        $blockedUser = new BlockedUser();
        $blockedUser->object_id = $this->user->id;
        $blockedUser->subject_id = $this->get('user_id');
        return $blockedUser;
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
            'user_id'=>'required|exists:users,id|not_already_blocked'
        ];
    }

    public function messages()
    {
        return [
            'user_id.not_already_blocked' => 'user already blocked'
        ];
    }
}
