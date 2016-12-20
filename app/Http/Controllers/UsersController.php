<?php

namespace App\Http\Controllers;


use App\Http\Response;
use App\Repositories\CheckedinsRepository;
use App\Repositories\LikedUsersRepository;
use App\Repositories\UsersRepository;
use App\Traits\Transformers\UsersControllerTransformer;
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

class UsersController extends ParentController
{
    use UsersControllerTransformer;

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

    public function postCheckIn(CheckinUserRequest $request)
    {
        try{
            $this->checkIns->checkoutPreviousCheckIns($request->user->id);
            $this->checkIns->store($request->checkedIn());
            return $this->response->respond(['data'=>[
                'checkIns' => $this->transformCheckins($this->users->getCheckInsAtLocation($request->get('location_id'),$request->user->id))
            ]]);
        }catch (\Exception $e){
            return $this->response->respondInternalServerError($e->getMessage());
        }
    }

    public function like(LikeUserRequest $request)
    {
        try{
            $this->likes->store($request->likedUser());
            return $this->response->respond();
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
        $this->users->blockUser($request->blockedUserModel());
        return $this->response->respond();
    }

    public function unblock(UnblockUserRequest $request)
    {
        $this->users->unblockUser(['object_id'=>$request->user->id, 'subject_id'=> $request->get('user_id')]);
        return $this->response->respond();
    }

    public function blockedUsers(GetBlockedUsersRequest $request)
    {
        return $this->response->respond(['data'=>[
            'users' => $this->users->getBlockedUsersByUserId($request->user->id)
        ]]);
    }

    public function updateInterests(UpdateUserInterestsRequest $request)
    {
        $this->users->updateInterestsWhere(['user_id'=>$request->user->id],['age_min' => $request->get('age_min'), 'age_max'=>$request->get('age_max'), 'gender'=> $request->get('gender')]);
        $this->users->updateWhere(['id'=>$request->user->id],['about'=>$request->get('about')]);
        return $this->response->respond(['data'=>[
            'user'=>$this->users->findById($request->user->id)
        ]]);
    }

    public function usersStatusOnLocation(GetUsersStatusOnLocationRequest $request)
    {
        return $this->response->respond(['data'=>[
            'datables' => $this->users->countDatablesAtLocation($request->get('location_id'), $request->user->id),
            'checkedIns' => $this->users->countCheckInsAtLocation($request->get('location_id'), $request->user->id),
        ]]);
    }

    public function deactivate(DeactivateUserRequest $request)
    {
        $this->users->updateWhere(['id'=>$request->user->id],['active'=>0]);
        return $this->response->respond();
    }

    public function heartBeat(HeartbeatRequest $request)
    {
        $this->checkIns->checkHeart($request->user->id);
        return $this->response->respond();
    }
}