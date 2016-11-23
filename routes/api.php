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

Route::get('/foo', function (Requests\Foo\FooRequest $request) {
    $user = new App\User();
    $user->id = 4;
    $user->name = "noman";
    $user->save();
    \App\User::get()->each(function($user, $key){
        /** @var \App\User $user */
        dd($user->toJson());
    });

})->middleware('requestHandler:Foo\FooRequest');
