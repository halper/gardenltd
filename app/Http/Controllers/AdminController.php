<?php

namespace App\Http\Controllers;

use App\Contract;
use App\Demand;
use App\Department;
use App\Equipment;
use App\Feature;
use App\File;
use App\Library\CarbonHelper;
use App\Library\TurkishChar;
use App\Manufacturing;
use App\Material;
use App\Module;
use App\Personnel;
use App\Photo;
use App\Pwunit;
use App\Rejection;
use App\Report;
use App\Sfile;
use App\Site;
use App\Staff;
use App\Stock;
use App\Subcontractor;
use App\Subdetail;
use App\Submaterial;
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

    public function approve(Demand $demand)
    {
        $demand->approval_status = 3;
        $demand->save();
        $tab = 1;
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
        $tab = 1;
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
            'contract' => 'required'
        ]);
        $per_arr = $request->all();
        if (!empty($request->get("iban"))) {
            $per_arr["iban"] = preg_replace("/\\s+/ ", "", $request->get("iban"));
        }
        $personnel = Personnel::create($per_arr);
        if (isset($per_arr["wage"])) {
            $per_arr["wage"] = str_replace(",", ".", $request->get("wage"));
        } else {
            $per_arr["wage"] = 1.0;
        }

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
            'mobile_code_id' => 'required',
            'mobile' => 'required | size:7',
            'email' => 'email',
            'tax_number' => 'required'
        ]);
        $sub_arr = $request->all();
        foreach ($sub_arr as $v => $k) {
            if (empty($k)) {
                unset($sub_arr[$v]);
            }
        }

        $subcontractor = Subdetail::create($sub_arr);

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
        foreach ($per_arr as $k => $v) {
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
            $contract = $per->contract;
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

    public function postDelPersonnel(Request $request)
    {
        Personnel::find($request->get('userDeleteIn'))->delete();
        Session::flash('flash_message', 'Personel silindi');
        return redirect()->back();
    }

    public function getAltyukleniciDuzenle(Subdetail $subdetail)
    {
        return view('landing/subcontractor', compact('subdetail'));
    }

    public function postModifySubcontractor(Request $request)
    {
        $sub_arr = $request->all();
        $sub = Subdetail::find($request->get('id'));
        unset($sub_arr["_token"]);
        unset($sub_arr["id"]);
        foreach ($sub_arr as $k => $v) {
            $sub->$k = $v;
        }
        $sub->save();

        Session::flash('flash_message', 'Alt yüklenici güncellendi');
        return redirect()->back();

    }

    public function postDelSubcontractor(Request $request)
    {
        Subdetail::find($request->get('userDeleteIn'))->delete();
        Session::flash('flash_message', 'Alt yüklenici silindi');
        return redirect()->back();
    }

    public function postModifyDepartment(Request $request)
    {
        $eq = Department::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifyMaterial(Request $request)
    {
        $eq = Material::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifyEquipment(Request $request)
    {
        $eq = Equipment::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifyStaff(Request $request)
    {
        $eq = Staff::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifyManufacturing(Request $request)
    {
        $eq = Manufacturing::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifySubmaterial(Request $request)
    {
        $eq = Submaterial::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifyFeature(Request $request)
    {
        $eq = Feature::find($request->get("pk"));
        return $this->modifyEntry($eq, $request);
    }

    public function postModifyStock(Request $request)
    {
        $eq = Stock::find($request->get("pk"));
        if (!empty($eq)) {
            if ($request->name == 'stockName') {
                if (empty($request->get("value"))) {
                    $eq->delete();
                } else {
                    $eq->name = TurkishChar::tr_up($request->get("value"));
                    $eq->save();
                }
            } elseif ($request->name == 'stockUnit') {
                $eq->unit = TurkishChar::tr_up($request->get("value"));
                $eq->save();
            } elseif ($request->name == 'stockTotal') {
                $eq->total = $request->get("value");
                $eq->save();
            }
        }
        return response('success', 200);
    }

    public
    function getDepartments()
    {
        return Department::all()->toJson();
    }

    public
    function postAddSubmaterial(Request $request)
    {

        $material = Material::find($request->get("material"));
        $names = preg_replace('/(\s+;\s|\s+;|;\s+)/i', ';', $request->get("name"));
        $names = explode(';', $names);

        foreach ($names as $name) {
            $submaterial = $request->get("is_sm") == 1 ? new Submaterial() : new Feature();
            $submaterial->name = TurkishChar::tr_up($name);
            $submaterial->material()->associate($material);
            $submaterial->save();
        }
        Session::flash('flash_message', 'Bağlantılı malzeme eklendi');
        return redirect()->back();
    }

    public
    function getRetrieveManufacturings()
    {
        $resp_arr = [];
        foreach (Manufacturing::all() as $man) {
            array_push($resp_arr, $man->name);
        }
        return response($resp_arr, 200);
    }

    public
    function postAddManufacturing(Request $request)
    {
        Manufacturing::create($request->all());
        return response('success', 200);
    }

    public
    function getRetrieveStocks()
    {
        $resp_arr = [];
        foreach (Stock::all() as $stock) {
            array_push($resp_arr, [
                'name' => $stock->name,
                'total' => $stock->total,
                'unit' => $stock->unit
            ]);
        }
        return response($resp_arr, 200);
    }

    public
    function postAddStock(Request $request)
    {
        Stock::create($request->all());
        return response('success', 200);
    }

    public function postCreateReport(Request $request)
    {
        $site = Site::find($request->sid);
        $user = User::find($request->uid);
        $total_date = $this->getTotalDate($request->get('start_date'), $request->get('end_date'));
        for ($x = $total_date; $x >= 0; $x--) {
            $rep_date = Carbon::parse($request->get('end_date'))->subDays($x)->toDateString();
            if($site->report()->whereDate('created_at', '=', $rep_date)->get()->isEmpty()){
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
            if($site->report()->whereDate('created_at', '=', $rep_date)->get()->isEmpty()){
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
            }else{
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
    function modifyEntry($eq, $request)
    {
        if (!empty($eq)) {
            if (empty($request->get("value"))) {
                $eq->delete();
            } else {
                $eq->name = TurkishChar::tr_up($request->get("value"));
                $eq->save();
            }
        }
        return response(200);
    }

    private
    function uploadFile($file, $directory = null)
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
