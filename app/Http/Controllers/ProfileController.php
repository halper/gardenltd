<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Session;

class ProfileController extends ManagementController
{

    public function getIndex()
    {
        return view('profile');
    }
    public function postUpdate(Request $request){
        $user = Auth::user();
        $this->validate($request, [
            'name' => 'required',
            'employer' => 'required',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        if (!empty($request->get("password"))) {
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
                'employer' => $request->get('employer'),
            ])->save();


        Session::flash('flash_message', 'Kullanıcı bilgileri güncellendi');
        return redirect()->back();
    }
}
