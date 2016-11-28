<?php

namespace App\Http\Controllers;


use App\Repositories\CheckedinsRepository;
use App\Repositories\UsersRepository;
use Requests\CheckinUserRequest;

class UsersController extends ParentController
{
    public $users = null;
    public $checkIns = null;
    public function __construct(UsersRepository $users, CheckedinsRepository $checkedIns)
    {
        $this->users = $users;
        $this->checkIns = $checkedIns;
    }

    public function postCheckIn(CheckinUserRequest $request)
    {
        return $request->all();
    }

    public function getCheckedInUsers()
    {

    }
}
