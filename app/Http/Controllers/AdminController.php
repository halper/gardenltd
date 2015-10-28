<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Session;

class AdminController extends ManagementController
{
    //
    protected $data;

    function __construct()
    {
        $this->data = array();
        $this->middleware('admin');
    }

    public function getIndex()
    {
        return redirect('admin/ayarlar');
    }

    public function patchDelUser(Request $request)
    {
        $this->validate($request, [
            'userDeleteIn' => 'required'
        ]);

        $r_user = User::find($request->get('userDeleteIn'));
        $r_user_name = User::find($request->get('userDeleteIn'))->name;
        $r_user->delete();
        Session::flash("flash_message", "Kullanıcı <em>". $r_user_name."</em> silindi");


        return redirect('admin/ayarlar');
    }

    public function postAddUser(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        User::create(
            [
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => bcrypt($request->get('password'))
            ]);

        Session::flash('flash_message', 'Yeni kullanıcı <em>'. $request->get('name') .'</em> oluşturuldu');
        return redirect('admin/ayarlar');
    }

    public function getAyarlar()
    {
        $users = User::all();
        return view('landing/ayarlar', compact('users'));
    }

    public function edit(User $user)
    {
        return view('landing/edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
        ]);

        if(!empty($request->get("password"))){
            $this->validate($request, [
                'password' => 'required|confirmed|min:6',
            ]);

            $user->fill(
                [
                    'password' => bcrypt($request->get('password'))
                ]);
        }

            $user->fill(
                [
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                ])->save();


        Session::flash('flash_message', 'Kullanıcı bilgileri güncellendi');
        return redirect("admin/duzenle/$user->id");
    }
}
