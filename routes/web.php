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
    \Illuminate\Support\Facades\Storage::disk('s3')->put('photos', new File(public_path('favicon.ico')), 'public');
});
