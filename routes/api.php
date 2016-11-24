<?php
function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

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

Route::get('/foo', function (Requests\Foo\FooRequest $request) {
    $user = new \App\User();
    $user->email = uniqid()."@gmail.com";
    $user->password = '111';
    $user->name = 'noman';
    $user->remember_token = "8979";
    $user->save();
    /** @var \App\User $user */
    $user = \App\User::find(7);
    return $user->blockedUsers;
})->middleware('requestHandler:Foo\FooRequest');
