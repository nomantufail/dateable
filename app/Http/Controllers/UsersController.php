<?php

namespace App\Http\Controllers;


use App\Http\Response;
use App\Repositories\CheckedinsRepository;
use App\Repositories\UsersRepository;
use Requests\BlockUserRequest;
use Requests\CheckinUserRequest;
use Requests\CheckoutUserRequest;
use Requests\GetUsersStatusOnLocationRequest;

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
        try{
            $this->checkIns->checkoutPreviousCheckIns($request->user->id);
            return $this->response->respond(['data'=>[
                'checkIn' =>$this->checkIns->store($request->checkedIn())
            ]]);
        }catch (\Exception $e){
            return $this->response->respondInternalServerError($e->getMessage());
        }
    }

    public function checkoutUser(CheckoutUserRequest $request)
    {
        return $this->checkIns->checkoutPreviousCheckIns($request->user->id)?$this->response->respond([]):$this->response->respondInternalServerError();
    }

    public function block(BlockUserRequest $request)
    {
        return $this->users->blockUser($request->blockedUserModel());
    }

    public function usersStatusOnLocation(GetUsersStatusOnLocationRequest $request)
    {
        return $this->response->respond(['data'=>[
            'datables' => $this->users->countDatablesAtLocation($request->get('location_id'), $request->user->id),
            'checkedIns' => $this->users->countCheckInsAtLocation($request->get('location_id')),
        ]]);
    }
}
