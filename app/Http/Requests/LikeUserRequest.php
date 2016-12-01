<?php

namespace Requests;

use App\Models\LikedUser;
use Requests\Request;

class LikeUserRequest extends Request
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
            'user_id' => 'required|exists:users,id'
        ];
    }

    public function likedUser()
    {
        $liked =  new LikedUser();
        $liked->object_id = $this->user->id;
        $liked->subject_id = $this->get('user_id');
        return $liked;
    }
}
