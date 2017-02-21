<?php

namespace Requests;

use App\Models\CheckedIn;
use Carbon\Carbon;
use Requests\Request;

class CheckinUserRequest extends Request
{

    public function __construct(){
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
            'location_id' => 'required'
        ];
    }

    public function checkedIn()
    {
        $checkedIn = new CheckedIn();
        $checkedIn->location_id = $this->get('location_id');
        $checkedIn->lat = $this->get('lat');
        $checkedIn->long = $this->get('long');
        $checkedIn->user_id = $this->user->id;
        $checkedIn->checked_in = Carbon::now()->toDateTimeString();
        $checkedIn->created_at = Carbon::now()->toDateTimeString();
        $checkedIn->updated_at = Carbon::now()->toDateTimeString();
        return $checkedIn;
    }
}
