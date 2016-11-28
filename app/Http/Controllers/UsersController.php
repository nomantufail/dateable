<?php

namespace App\Http\Controllers;


use App\Http\Response;
use App\Repositories\CheckedinsRepository;
use App\Repositories\UsersRepository;
use Requests\CheckinUserRequest;

class UsersController extends ParentController
{
    public $users = null;
    public $checkIns = null;
    public $response = null;
    public function __construct(UsersRepository $users, CheckedinsRepository $checkedIns)
    {
        $this->users = $users;
        $this->checkIns = $checkedIns;
        $this->response = new Response();
    }

    public function postCheckIn(CheckinUserRequest $request)
    {
        $this->checkIns->checkoutPreviousCheckIns($request->user->id);
        return $this->response->respond(['data'=>[
            'checkIn' =>$this->checkIns->store($request->checkedIn())
        ]]);
    }

    public function getCheckedInUsers()
    {

    }
}
