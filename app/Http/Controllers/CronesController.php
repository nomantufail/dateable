<?php

namespace App\Http\Controllers;


use App\Http\Response;
use App\Repositories\CheckedinsRepository;
use App\Repositories\LikedUsersRepository;
use App\Repositories\UsersRepository;
use Requests\BlockUserRequest;
use Requests\CheckinUserRequest;
use Requests\CheckoutUserRequest;
use Requests\CroneRequest;
use Requests\DeactivateUserRequest;
use Requests\GetBlockedUsersRequest;
use Requests\GetUsersStatusOnLocationRequest;
use Requests\HeartbeatRequest;
use Requests\LikeUserRequest;
use Requests\UnblockUserRequest;
use Requests\UpdateUserInterestsRequest;

class CronesController extends ParentController
{
    public $users = null;
    public $checkIns = null;
    public $response = null;
    public $likes = null;
    public function __construct(UsersRepository $users, CheckedinsRepository $checkedIns)
    {
        $this->users = $users;
        $this->checkIns = $checkedIns;
        $this->response = new Response();
        $this->likes = new LikedUsersRepository();
    }

    public function autoCheckout()
    {
        return $this->checkIns->autoCheckout();
    }

}
