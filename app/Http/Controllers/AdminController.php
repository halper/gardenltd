<?php

namespace App\Http\Controllers;

use App\Module;
use App\Site;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    //

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
        Session::flash("flash_message", "Kullanıcı <em>" . $r_user_name . "</em> silindi");


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

        Session::flash('flash_message', 'Yeni kullanıcı <em>' . $request->get('name') . '</em> oluşturuldu');
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
            ])->save();


        Session::flash('flash_message', 'Kullanıcı bilgileri güncellendi');
        return redirect("admin/duzenle/$user->id");
    }

    public function editSitePermissions(Request $request, User $user)
    {
        $user->site()->detach();


        $inputs = $request->all();
        if (array_key_exists("sites", $inputs)) {
            foreach ($inputs["sites"] as $site_id) {
                $user->site()->attach($site_id);
            }
        }
        Session::flash('flash_message', 'Kullanıcı şantiyeleri tanımlandı');
        return redirect("admin/duzenle/$user->id");
    }

    public function editModulePermissions(Request $request, User $user)
    {
        $user->permission()->detach();
        $inputs = $request->all();
        if (array_key_exists("modules", $inputs)) {
            foreach ($inputs["modules"] as $module_permission) {
                $permission_id = (int)$module_permission % 10;
                $module = (int)$module_permission / 10;
                $module = floor($module);
                echo("\n".$module);
                echo("\n".$permission_id);
                $user->permission()->attach($permission_id, ["module_id" => $module]);
            }
        }
        Session::flash('flash_message', 'Kullanıcı izinleri tanımlandı');
        return redirect("admin/duzenle/$user->id");
    }
}
