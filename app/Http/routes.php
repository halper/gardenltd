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

Route::get('garden/public/auth/login', 'Auth\AuthController@getLogin');
Route::post('garden/public/auth/login', 'Auth\AuthController@postLogin');
Route::get('garden/public/auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('garden/public/auth/register', 'Auth\AuthController@getRegister');
Route::post('garden/public/auth/register', 'Auth\AuthController@postRegister');

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
    Route::get('garden/public/tekil/{site}/baglanti-malzeme-duzenle/{smdemand}', 'TekilController@getBaglantiMalzemeDuzenle');
    Route::get('garden/public/tekil/{site}/talep-duzenle/{demand}', 'TekilController@getTalepDuzenle');
    Route::get('garden/public/tekil/{site}/talep-sevket/{demand}', 'TekilController@getTalepSevket');
    Route::get('garden/public/tekil/{site}/alt-yuklenici-duzenle/{subcontractor}', 'TekilController@getAltYukleniciDuzenle');
    Route::get('garden/public/tekil/{site}/alt-yuklenici-duzenle/{subcontractor}/personel-duzenle/{personnel}', 'TekilController@getPersonelDuzenle');
    Route::controller('garden/public/tekil/{site}', 'TekilController');
});


Route::get('garden/public/santiye/retrieve-stocks', 'SantiyeController@getStocks');
Route::get('garden/public/santiye-duzenle/{site}', 'SantiyeController@editSite');
Route::post('garden/public/santiye/modify-stock', 'SantiyeController@updateStock');
Route::controller('garden/public/santiye', 'SantiyeController');

Route::group(['middleware' => ['auth', 'admin']], function() {
    Route::get('garden/public/admin/duzenle/{user}', 'AdminController@edit');
    Route::get('garden/public/admin/talep-onay/{demand}', 'AdminController@approve');
    Route::get('garden/public/admin/duzenle', function(){
        return new RedirectResponse(url('garden/public//admin'));
    });
    Route::patch('garden/public/admin/update/{user}', 'AdminController@update');
    Route::patch('garden/public/admin/sites/{user}', 'AdminController@editSitePermissions');
    Route::patch('garden/public/admin/modules/{user}', 'AdminController@editModulePermissions');
    Route::get('garden/public/admin/personel-duzenle/{personnel}', 'AdminController@getPersonelDuzenle');
    Route::get('garden/public/admin/altyuklenici-duzenle/{subdetail}', 'AdminController@getAltyukleniciDuzenle');
    Route::controller('garden/public/admin', 'AdminController');
});


Route::bind('directory', function($directory){
    return $directory;
});

Route::bind('filename', function($filename){
    return $filename;
});

Route::get('garden/public/uploads/{directory}/{filename}', 'HomeController@getUploads');
Route::get('garden/public//home', function(){
   return redirect('garden/public/santiye');
});
Route::controller('garden/public/ekle', 'EkleController');

Route::get('garden/public/guncelle/personel-duzenle/{personnel}', 'GuncelleController@getPersonelDuzenle');
Route::get('garden/public/guncelle/altyuklenici-duzenle/{subdetail}', 'GuncelleController@getAltyukleniciDuzenle');
Route::controller('garden/public/guncelle', 'GuncelleController');
Route::controller('garden/public/bilgilerim', 'ProfileController');
Route::controller('garden/public/common', 'CommonController');
Route::controller('garden/public/', 'HomeController');
