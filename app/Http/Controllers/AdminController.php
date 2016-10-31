<?php

namespace App\Http\Controllers;

use App\Contract;
use App\Demand;
use App\Department;
use App\Equipment;
use App\Expdetail;
use App\Feature;
use App\File;
use App\Group;
use App\Iddoc;
use App\Library\CarbonHelper;
use App\Library\TurkishChar;
use App\Manufacturing;
use App\Material;
use App\Personnel;
use App\Photo;
use App\Pwunit;
use App\Rejection;
use App\Report;
use App\Salary;
use App\Site;
use App\SpecialPermission;
use App\Staff;
use App\Stock;
use App\Subcontractor;
use App\Subdetail;
use App\Submaterial;
use App\Tag;
use App\User;
use App\Wage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    //
    const SEKME_BIR = 1;
    const SEKME_IKI = 2;
    const SEKME_UC = 3;
    const SEKME_DORT = 4;

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

    public function postAddGroup(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $group = Group::create(
            [
                'name' => TurkishChar::tr_up($request->get('name')),
            ]);


        Session::flash('flash_message', 'Yeni grup <em>' . $group->name . '</em> oluşturuldu');
        return redirect()->back()->with('tab', self::SEKME_DORT);
    }

    public function postAddUsersToGroup(Request $request)
    {
        Group::find($request->group_id)->user()->detach();
        if ($request->has('users')) {
            foreach ($request->get("users") as $user_id) {
                $user = User::find($user_id);
                $user->group()->attach($request->group_id);
            }
        }

        Session::flash('flash_message', "Grup kullanıcıları güncellendi!");

        return redirect()->back();
    }

    public function postAddSitesToGroup(Request $request)
    {
        Group::find($request->group_id)->site()->detach();
        if (!empty($request->get("sites"))) {
            foreach ($request->get("sites") as $site_id) {
                $site = Site::find($site_id);
                $site->group()->attach($request->group_id);
            }
        }

        Session::flash('flash_message', "Şantiye erişimleri güncellendi!");

        return redirect()->back()->with('tab', self::SEKME_IKI);
    }

    public function postAddModulesToGroup(Request $request)
    {
        $group = Group::find($request->group_id);
        $group->permission()->detach();
        $inputs = $request->all();
        if (array_key_exists("modules", $inputs)) {
            foreach ($inputs["modules"] as $module_permission) {
                $permission_id = (int)$module_permission % 10;
                $module = (int)$module_permission / 10;
                $module = floor($module);
                $group->permission()->attach($permission_id, ["module_id" => $module]);
            }
        }

        Session::flash('flash_message', "Modül erişimleri güncellendi!");

        return redirect()->back()->with('tab', self::SEKME_UC);
    }

    public function postAddSpecialPermissionsToGroup(Request $request)
    {
        $group = Group::find($request->group_id);
        $group->specialPermission()->detach();
        if (!empty($request->get('special-permissions'))) {
            foreach ($request->get('special-permissions') as $special_permission_id) {
                $special_permission = SpecialPermission::find($special_permission_id);
                $group->specialPermission()->attach($special_permission);
            }
        }
        Session::flash('flash_message', "Özel izinler güncellendi!");

        return redirect()->back()->with('tab', self::SEKME_DORT);
    }

    public function getAyarlar()
    {
        $users = User::all();
        return view('landing.ayarlar', compact('users'));
    }

    public function edit(User $user)
    {
        return view('landing.edit', compact('user'));
    }

    public function groupEdit(Group $group)
    {
        return view('landing.edit-group', compact('group'));
    }

    public function approve(Demand $demand)
    {
        $demand->approval_status = 3;
        $demand->save();
        $tab = self::SEKME_BIR;
        Session::flash('flash_message', 'Talep onaylandı');
        return redirect()->back()->with('tab', $tab);
    }

    public function postRejectDemand(Request $request)
    {
        $demand = Demand::find($request->demand_id);
        $demand->approval_status = 4;
        $demand->save();
        $rejection = new Rejection();
        $rejection->reason = $request->reason;
        $rejection->demand()->associate($demand);
        $rejection->user()->associate(\Auth::user());
        $rejection->save();
        $tab = self::SEKME_BIR;
        Session::flash('flash_message', 'Talep reddedildi!');
        return redirect()->back()->with('tab', $tab);
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
        return redirect()->back();
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

    public
    function getDepartments()
    {
        return Department::all()->toJson();
    }


    public function postCreateReport(Request $request)
    {
        $site = Site::find($request->sid);
        $user = User::find($request->uid);
        $total_date = $this->getTotalDate($request->get('start_date'), $request->get('end_date'));
        for ($x = $total_date; $x >= 0; $x--) {
            $rep_date = Carbon::parse($request->get('end_date'))->subDays($x)->toDateString();
            if ($site->report()->whereDate('created_at', '=', $rep_date)->get()->isEmpty()) {
                $report = new Report();
                $report->created_at = $rep_date;
                $report->site()->associate($site);
                $report->save();
                $pwunit = new Pwunit();
                $staff = Staff::find(1);
                $pwunit->staff()->associate($staff);
                $pwunit->report()->associate($report);
                $pwunit->save();
                foreach ($site->equipment()->get() as $eq) {
                    $report->equipment()->attach($eq->id);
                }
                $report->user()->attach($user);
            }
        }
        return response('success', 200);
    }

    public function postMakeReportable(Request $request)
    {
        $site = Site::find($request->sid);
        $user = User::find($request->uid);
        $total_date = $this->getTotalDate($request->get('start_date'), $request->get('end_date'));
        for ($x = $total_date; $x >= 0; $x--) {
            $rep_date = Carbon::parse($request->get('end_date'))->subDays($x)->toDateString();
            if ($site->report()->whereDate('created_at', '=', $rep_date)->get()->isEmpty()) {
                $report = new Report();
                $report->created_at = $rep_date;
                $report->site()->associate($site);
                $report->save();
                $pwunit = new Pwunit();
                $staff = Staff::find(1);
                $pwunit->staff()->associate($staff);
                $pwunit->report()->associate($report);
                $pwunit->save();
                foreach ($site->equipment()->get() as $eq) {
                    $report->equipment()->attach($eq->id);
                }
            } else {
                $report = $site->report()->whereDate('created_at', '=', $rep_date)->first();
            }
            $report->user()->attach($user);
        }
        return response('success', 200);
    }

    public function postCloseReport(Request $request)
    {
        $user = User::find($request->uid);
        $user->report()->detach(Report::find($request->id));
        return response('success', 200);
    }


    private function getTotalDate($start_date, $end_date)
    {
        $start_date = date_create($start_date);

        $end_date = date_create($end_date);
        $total_date = str_replace("+", "", date_diff($start_date, $end_date)->format("%R%a"));
        return (int)$total_date;
    }

    private
    function uploadFile($file, $directory = null)
    {
        if (empty($directory)) {
            $directory = public_path() . '/uploads/' . uniqid(rand(), true);
        }
        $filename = $file->getClientOriginalName();

        $mime = $file->getMimeType();
        $UPLOADABLE_FILE_TYPES = ['application/pdf', 'image/png', 'image/jpeg'];
        if (in_array($mime, $UPLOADABLE_FILE_TYPES)) {
            if ($file->move($directory, $filename))

                return File::create([
                    "name" => $filename,
                    "path" => $directory
                ]);
        } else
            return 'format';
        return null;
    }

}
