<?php

namespace App\Http\Controllers;

use App\Department;
use App\Equipment;
use App\File;
use App\Library\CarbonHelper;
use App\Manufacturing;
use App\Module;
use App\Sfile;
use App\Site;
use App\Staff;
use App\Subcontractor;
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
            'employer' => 'required',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create(
            [
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'employer' => $request->get('employer'),
                'password' => bcrypt($request->get('password'))
            ]);

        if (array_key_exists("admin", $request->all())) {
            $user->permission()->attach(3, ["module_id" => 1]);
        }

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
            'employer' => 'required',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        if (!array_key_exists("admin", $request->all())) {
            if ($user->isAdmin()) {
                $user->permission()->detach(3);
            }
        } else {
            $user->permission()->attach(3, ["module_id" => 1]);
        }

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
                echo("\n" . $module);
                echo("\n" . $permission_id);
                $user->permission()->attach($permission_id, ["module_id" => $module]);
            }
        }
        Session::flash('flash_message', 'Kullanıcı izinleri tanımlandı');
        return redirect("admin/duzenle/$user->id");
    }

    public function getEkle()
    {
        return view('landing/insert');
    }

    public function postAddSubcontractor(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'city_id' => 'required',
            'official' => 'required',
            'title' => 'required',
            'area_code_id' => 'required',
            'phone' => 'required | size:7',
            'fax_code_id' => 'required',
            'fax' => 'required | size:7',
            'mobile_code_id' => 'required',
            'mobile' => 'required | size:7',
            'email' => 'email',
            'tax_number' => 'required'
        ]);

        $subcontractor = Subcontractor::create($request->all());

        foreach ($request->get('manufacturings') as $man_id) {
            Manufacturing::find($man_id)->subcontractor()->attach($subcontractor->id);
        }


        Session::flash('flash_message', "Taşeron ($subcontractor->name) kaydı oluşturuldu");

        return redirect()->back();

    }

    public function postAddEquipment(Request $request)
    {
        $eq = Equipment::create($request->all());

        return !empty($eq) ? response()->json('success', 200) : response()->json('error', 400);
    }

    public function postAddStaff(Request $request)
    {
        $st = Staff::create($request->all());
        return !empty($st) ? response()->json('success', 200) : response()->json('error', 400);
    }

    public function postAddDepartment(Request $request)
    {
        $dt = Department::create($request->all());
        return !empty($dt) ? response()->json($dt, 200) : response()->json('error', 400);
    }

}
