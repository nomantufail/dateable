<?php
use Illuminate\Http\File;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    dd(PushNotification::app('appNameAndroid')
        ->to("eeWU04zWBWM:APA91bF1FWNHtZFjc0kY7kq-LbrnVpPH-4VGPtSs_l5zgxzfHFlrW1N3m_Fm7emPPuTc6gToFPYLpsF2d4OxHEqGVMWXZRRngpL3X1m-Lkry8bf4X42RwlrkNsOtCmVx2B4n3ZM4hjlM")
        ->send('fuck you agha'));
});
