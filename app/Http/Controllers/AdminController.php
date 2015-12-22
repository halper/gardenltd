<?php

namespace App\Http\Controllers;

use App\Contract;
use App\Department;
use App\Equipment;
use App\File;
use App\Library\CarbonHelper;
use App\Manufacturing;
use App\Material;
use App\Module;
use App\Personnel;
use App\Photo;
use App\Sfile;
use App\Site;
use App\Staff;
use App\Subcontractor;
use App\Subdetail;
use App\User;
use App\Wage;
use Carbon\Carbon;
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

    public function postCheckTck(Request $request)
    {
        if (is_null(Personnel::where('tck_no', $request->get('tck_no'))->first())) {
            return response()->json('unique', 200);
        } else {
            return response()->json('found!', 200);
        }

    }

    public function postAddPersonnel(Request $request)
    {
        $this->validate($request, [
            'tck_no' => 'required | size:11',
            'name' => 'required',
            'contract' => 'required',
            'wage' => 'required'
        ]);
        $per_arr = $request->all();
        $per_arr["wage"] = str_replace(",", ".", $request->get("wage"));
        if (!empty($request->get("iban"))) {
            $per_arr["iban"] = preg_replace("/\\s+/ ", "", $request->get("iban"));
        }
        $personnel = Personnel::create($per_arr);
        $wage = Wage::create([
            'wage' => $per_arr["wage"],
            'since' => Carbon::parse($personnel->created_at)->toDateString()]);
        $wage->personnel()->associate($personnel);
        $wage->save();
        $directory = public_path() . '/uploads/' . uniqid(rand(), true);
        $contract_file = $this->uploadFile($request->file("contract"), $directory);
        $contract = Contract::create([
            'contract_date' => $request->get('contract_date'),
            'contract_start_date' => $request->get('contract_start_date'),
            'contract_end_date' => $request->get('contract_end_date'),
        ]);
        $contract->file()->save($contract_file);

        if (!empty($request->file("documents"))) {
            foreach ($request->file("documents") as $file) {
                $db_file = $this->uploadFile($file, $directory);

                if ($db_file) {
                    $photo = Photo::create();
                    $photo->file()->save($db_file);
                    $personnel->photo()->save($photo);
                }
            }
        }
        $personnel->contract()->save($contract);
        (new Site)->personnel()->save($personnel);

        Session::flash('flash_message', 'Personel eklendi');
        return redirect()->back();
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

        $subcontractor = Subdetail::create($request->all());

        Session::flash('flash_message', "Alt yüklenici ($subcontractor->name) kaydı oluşturuldu");

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

    public function postAddMaterial(Request $request)
    {
        $mt = Material::create($request->all());
        return !empty($mt) ? response()->json($mt, 200) : response()->json('error', 400);
    }

    public function getGuncelle()
    {
        return view('landing/modify');
    }

    public function getPersonelDuzenle(Personnel $personnel)
    {
        return view('landing/personnel', compact('personnel'));
    }

    public function postModifyPersonnel(Request $request)
    {
        $per_arr = $request->all();
        unset($per_arr["_token"]);
        $per_wage = str_replace(",", ".", $request->get("wage"));
        unset($per_arr["wage"]);
        unset($per_arr["id"]);
        unset($per_arr["contract"]);
        unset($per_arr["documents"]);
        if (!empty($request->get("iban"))) {
            $per_arr["iban"] = preg_replace("/\\s+/ ", "", $request->get("iban"));
        }

        $per = Personnel::find($request->get("id"));
        foreach($per_arr as $k => $v){
            $per->$k = $v;
        }
        $per->save();
        $wage = Wage::create([
            'wage' => $per_wage,
            'since' => Carbon::parse($per->updated_at)->toDateString()]);
        $wage->personnel()->associate($per);
        $wage->save();

        $directory = public_path() . '/uploads/' . uniqid(rand(), true);
        if (!empty($request->file("contract"))) {
            $contract_file = $this->uploadFile($request->file("contract"), $directory);
            $contract = $per->contract->first();
            $contract->file()->save($contract_file);
        }

        if (!empty($request->file("documents"))) {
            foreach ($request->file("documents") as $file) {

                $db_file = $this->uploadFile($file, $directory);

                if ($db_file) {
                    $photo = Photo::create();
                    $photo->file()->save($db_file);
                    $per->photo()->save($photo);
                }
            }
        }

        Session::flash('flash_message', 'Personel güncellendi');
        return redirect()->back();

    }

    public function postDeletePersonnelFiles(Request $request)
    {
        Photo::find($request->get("fileid"))->delete();
        return response('success', 200);
    }

    private function uploadFile($file, $directory = null)
    {
        if (empty($directory)) {
            $directory = public_path() . '/uploads/' . uniqid(rand(), true);
        }
        $filename = $file->getClientOriginalName();

        if ($file->move($directory, $filename))

            return File::create([
                "name" => $filename,
                "path" => $directory
            ]);

        return null;
    }

}
