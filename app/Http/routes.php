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
use Illuminate\Http\RedirectResponse;

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
Route::model('group', 'App\Group');
Route::model('smdemand', 'App\Smdemand');
Route::model('demand', 'App\Demand');
Route::model('personnel', 'App\Personnel');
Route::model('subdetail', 'App\Subdetail');
Route::model('subcontractor', 'App\Subcontractor');

Route::group(['middleware' => ['auth', 'access']], function() {
    Route::get('tekil/{site}/baglanti-malzeme-duzenle/{smdemand}', 'TekilController@getBaglantiMalzemeDuzenle');
    Route::get('tekil/{site}/talep-duzenle/{demand}', 'TekilController@getTalepDuzenle');
    Route::get('tekil/{site}/talep-sevket/{demand}', 'TekilController@getTalepSevket');
    Route::get('tekil/{site}/alt-yuklenici-duzenle/{subcontractor}', 'TekilController@getAltYukleniciDuzenle');
    Route::get('tekil/{site}/alt-yuklenici-duzenle/{subcontractor}/personel-duzenle/{personnel}', 'TekilController@getPersonelDuzenle');
    Route::controller('tekil/{site}', 'TekilController');
});


Route::get('santiye/retrieve-stocks', 'SantiyeController@getStocks');
Route::get('santiye-duzenle/{site}', 'SantiyeController@editSite');
Route::post('santiye/modify-stock', 'SantiyeController@updateStock');
Route::controller('santiye', 'SantiyeController');

Route::group(['middleware' => ['auth', 'admin']], function() {
    Route::get('admin/duzenle/{user}', 'AdminController@edit');
    Route::get('admin/talep-onay/{demand}', 'AdminController@approve');
    Route::get('admin/duzenle', function(){
        return new RedirectResponse(url('/admin'));
    });
    Route::patch('admin/update/{user}', 'AdminController@update');
    Route::patch('admin/sites/{user}', 'AdminController@editSitePermissions');
    Route::patch('admin/modules/{user}', 'AdminController@editModulePermissions');
    Route::get('admin/personel-duzenle/{personnel}', 'AdminController@getPersonelDuzenle');
    Route::get('admin/altyuklenici-duzenle/{subdetail}', 'AdminController@getAltyukleniciDuzenle');
    Route::controller('admin', 'AdminController');
});


Route::bind('directory', function($directory){
    return $directory;
});

Route::bind('filename', function($filename){
    return $filename;
});

Route::get('uploads/{directory}/{filename}', 'HomeController@getUploads');
Route::get('/home', function(){
   return redirect('santiye');
});
Route::controller('ekle', 'EkleController');

Route::get('guncelle/personel-duzenle/{personnel}', 'GuncelleController@getPersonelDuzenle');
Route::get('guncelle/altyuklenici-duzenle/{subdetail}', 'GuncelleController@getAltyukleniciDuzenle');
Route::controller('guncelle', 'GuncelleController');
Route::controller('bilgilerim', 'ProfileController');
Route::controller('common', 'CommonController');
Route::controller('/', 'HomeController');
