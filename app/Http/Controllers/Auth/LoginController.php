<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Response;
use App\Models\UserInterests;
use App\Repositories\UsersRepository;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Requests\FbLoginRequest;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    public $users = null;
    public $response = null;
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
        $this->response = new Response();
        $this->users = new UsersRepository();
    }

    public function fblogin(FbLoginRequest $request)
    {
        /** @var User $user */
        $user = $this->users->findByFbId($request->get('id'));
        if($user == null){
            $user = $this->users->store($request->getFbUser());
        }
        $user->access_token = bcrypt($request->get('id'));
        $user->active = 1;
        $user->save();
        /** @var UserInterests $interests */
        $interests = $user->interests;
        return $this->response->respond([
            'data'=>[
                'user' => $user,
                'interests' => (object)[
                    'gender' => ($interests != null)?$interests->gender:2,
                    'age'=>(object)[
                        'min' => ($interests != null)?$interests->age_min:18,
                        'max' => ($interests != null)?$interests->age_max:30,
                    ]
                ]
            ],
            'access_token' => $user->access_token
        ]);
    }
}
