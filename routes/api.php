<?php
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::post('/fblogin', 'Auth\LoginController@fblogin')->middleware('requestHandler:FbLoginRequest');
Route::post('/user/checkin', 'UsersController@postCheckIn')->middleware('requestHandler:CheckinUserRequest');
Route::post('/user/checkout', 'UsersController@checkoutUser')->middleware('requestHandler:CheckoutUserRequest');
Route::get('/location/users_status', 'UsersController@usersStatusOnLocation')->middleware('requestHandler:GetUsersStatusOnLocationRequest');
Route::post('/user/block', 'UsersController@block')->middleware('requestHandler:BlockUserRequest');
Route::post('/user/unblock', 'UsersController@unblock')->middleware('requestHandler:UnblockUserRequest');
Route::post('/user/like', 'UsersController@like')->middleware('requestHandler:LikeUserRequest');
Route::get('/users/blocked', 'UsersController@blockedUsers')->middleware('requestHandler:GetBlockedUsersRequest');
Route::post('/user/interests/update', 'UsersController@updateInterests')->middleware('requestHandler:UpdateUserInterestsRequest');
Route::post('/user/account/deactivate', 'UsersController@deactivate')->middleware('requestHandler:DeactivateUserRequest');
Route::post('/user/send_heartbeat', 'UsersController@heartBeat')->middleware('requestHandler:HeartbeatRequest');
Route::post('/crone', 'CronesController@autoCheckout')->middleware('requestHandler:AutoCheckoutRequest');
