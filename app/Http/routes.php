<?php

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

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

Route::bind('site', function($slug){
   return \App\Site::whereSlug($slug)->first();
});
Route::bind('module', function($slug){
   return \App\Module::whereSlug($slug)->first();
});

Route::model('user', 'App\User');

Route::get('tekil/{site}/yemek', 'Tekil\ModuleController@getYemek');
Route::get('tekil/{site}', 'TekilController@getSite');


Route::controller('santiye', 'SantiyeController');
Route::get('admin/duzenle/{user}', 'AdminController@edit');
Route::patch('admin/update/{user}', 'AdminController@update');
Route::controller('admin', 'AdminController');


Route::controller('/', 'HomeController');
