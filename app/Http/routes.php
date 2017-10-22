<?php
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/',function (){
   return view('master');
});
Route::post('auth', 'UserController@checkAuth');

Route::group(['middleware' => 'auth-api'], function () {
   	Route::resource('user','UserController');
	Route::resource('gallery','GalleryController');

	Route::post('upload-image', 'GalleryController@uploadImage');

	Route::post('delete-single-image', 'GalleryController@deleteSingleImage');
});


use Illuminate\Support\Facades\Auth;
Route::get('test', function ()
{
	Auth::logout();
}); 