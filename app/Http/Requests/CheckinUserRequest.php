<?php

namespace Requests;

use App\Models\CheckedIn;
use Requests\Request;

class CheckinUserRequest extends Request
{

    public function __construct(){
        $this->authenticatable = true;
        $this->user = $this->user();
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

    public function checkedIn()
    {
        $checkedIn = new CheckedIn();
        $checkedIn->lat = $this->get('lat');
        $checkedIn->long = $this->get('long');
        $checkedIn->user_id = $this->user->id;
        $checkedIn->checked_in = date('Y-m-d H:i:s');
        return $checkedIn;
    }
}
