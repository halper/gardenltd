<?php

namespace App\Http\Controllers;

use App\Account;
use App\Contract;
use App\Cost;
use App\Demand;
use App\Expense;
use App\Fee;
use App\File;
use App\Inmaterial;
use App\Library\CarbonHelper;
use App\Library\Weather;
use App\Manufacturing;
use App\Material;
use App\Module;
use App\Outmaterial;
use App\Photo;
use App\Pwunit;
use App\Receipt;
use App\Report;
use App\Site;
use App\Http\Requests;
use App\Subcontractor;
use App\Swunit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class TekilController extends Controller
{
    //


    public function getIndex(Site $site, Module $modules)
    {
        return view('tekil/main', compact('site', 'modules'));
    }

    public function getGunlukRapor(Site $site, Module $modules)
    {
        $report = new Report;

        if (empty($report->where('created_at', Carbon::now()->toDateString())->where('site_id', $site->id)->first())) {
            $report->site_id = $site->id;
            $report->save();

        } else {
            $report = $report->where('created_at', Carbon::now()->toDateString())->where('site_id', $site->id)->first();
        }
        if (session()->has('report')) {
            $report = session()->get('report');
        }
        $yesterdays_report = $site->report()->where('created_at', Carbon::yesterday()->toDateString())->first();
        if(is_null($yesterdays_report->weather)){
            $wt = new Weather(1);
            $yesterdays_report->weather = $wt->getDescription();
            $yesterdays_report->temp_min = $wt->getMin();
            $yesterdays_report->temp_max = $wt->getMax();
            $yesterdays_report->wind = $wt->getWind();
            $yesterdays_report->degree = $wt->getDirection();
            $yesterdays_report->save();
        }

        return view('tekil/daily', compact('site', 'modules', 'report'));
    }


    public function getMalzemeTalep(Site $site, Module $modules)
    {
        if (session()->has("material_array")) {
            $material_array = session("material_array");
            return view('tekil/demand', compact('site', 'modules', 'material_array'));
        } else
            return view('tekil/demand', compact('site', 'modules'));
    }

    public function postAddMaterials(Request $request)
    {
        $material_array = $request->get("materials");
        return redirect()->back()->with("material_array", $material_array);
    }

    public function postDemandMaterials(Request $request, Site $site)
    {
        $demand = new Demand;
        $demand->demand = $site->job_name . "_" . Carbon::now('Europe/Istanbul')->format('mdY');
        $demand->save();
        foreach ($request->get("materials") as $mat) {
            $my_mat = Material::find($mat);
            $my_mat->demands()->attach($demand->id, ["quantity" => $request->get("quantity")[$mat - 1],
                "unit" => $request->get("unit")[$mat - 1],]);
        }
        Session::flash('flash_message', 'Malzeme talep formu oluşturuldu');
        return redirect()->back();
    }

    public function postSelectDate(Request $request, Site $site)
    {
        $data = ["date" => $request->get("date")];
        $report_date = $data["date"];
        $sql_date = CarbonHelper::getMySQLDate($report_date);

        if (!empty(Report::where('created_at', $sql_date)->where('site_id', $site->id)->first())) {
            $report = Report::where('created_at', $sql_date)->where('site_id', $site->id)->first();
            return redirect()->back()->with(["data" => $data, "report" => $report]);

        } else {
            $report = Report::where('created_at', Carbon::now()->toDateString())->where('site_id', $site->id)->first();
            Session::flash('flash_message_error', 'Belirtilen tarihe ait rapor bulunamadı');
            return redirect()->back()->with(["report" => $report]);
        }

    }

    public function postSaveStaff(Request $request)
    {
//        Main contractor
        $staff_arr = $request->get("staffs");
        $cont_arr = $request->get("contractor-quantity");

        $report = Report::find($request->get("report_id"));
        $report->staff()->detach();
        for ($i = 0; $i < sizeof($staff_arr); $i++) {
            if (!strlen($cont_arr[$i]) == 0) {
                $report->staff()->attach($staff_arr[$i], ["quantity" => $cont_arr[$i]]);
                Session::flash('flash_message', 'İlgili personel eklendi');
            }
        }
        return redirect()->back();
    }

    public function postDetachStaff(Request $request)
    {
//        Main contractor
        $staff_id = $request->get("staffid");
        $report = Report::find($request->get("report_id"));
        $report->staff()->detach($staff_id);
        return response()->json('success', 200);
    }

    public function postAddManagementStaffs(Site $site, Request $request)
    {
        $report = Report::find($request->get("report_id"));
        $main_staff_arr = $request->get("main-staffs");
        $q_arr = $request->get("main-staff-quantity");
        $exists = false;
        for ($i = 0; $i < sizeof($main_staff_arr); $i++) {
            switch ($main_staff_arr[$i]) {
                case 0:
                    $report->management_staff = $q_arr[$i];
                    $exists = true;
                    break;
                case 1:
                    $report->employer_staff = $q_arr[$i];
                    $exists = true;
                    break;
                case 2:
                    $report->building_control_staff = $q_arr[$i];
                    $exists = true;
                    break;
                case 3:
                    $report->isg_staff = $q_arr[$i];
                    $exists = true;
                    break;
            }
        }
        if ($exists) {
            $report->save();
        }
        Session::flash('flash_message', 'Personel kaydı başarılı');
        return redirect()->back();
    }

    public function postDeleteManagementStaff(Request $request)
    {
        $report = Report::find($request->get("reportid"));
        $column_name = $request->get("column");
        $report->$column_name = "0";
        $report->save();
        return response()->json('success', 200);
    }

    public function postSaveSubcontractorStaff(Request $request)
    {
//        Daily report subcontractor staff

        $report = Report::find($request->get("report_id"));

        if (is_null($request->get("subcontractor_staffs")) || is_null($request->get("substaff-quantity"))) {
            Session::flash('flash_message_error', 'Taşeron personeli eklemelisiniz');
            return redirect()->back();
        }
        $staffs = $request->get("subcontractor_staffs");
        $q_arr = $request->get("substaff-quantity");

        for ($i = 0; $i < sizeof($staffs); $i++) {
            if ($report->hasSubstaff($staffs[$i], $request->get("subcontractor"))) {
                $report->detachSubstaff($staffs[$i], $request->get("subcontractor"));
            }
            $report->substaff()->attach($staffs[$i], ["quantity" => $q_arr[$i], "subcontractor_id" => $request->get("subcontractor")]);
        }
        Session::flash('flash_message', 'Taşeron personel kaydı başarılı');
        return redirect()->back();
    }

    public function postDeleteReportSubcontractor(Request $request)
    {
        $report = Report::find($request->get("reportid"));
        $subcontractor_id = $request->get("subcontractorid");
        $substaffs = $report->substaff()->where("subcontractor_id", $subcontractor_id)->get();
        $report->subcontractor()->detach($subcontractor_id);
        foreach ($substaffs as $substaff) {
            $report->substaff()->detach($substaff->id);
        }
        return response()->json('success', 200);
    }


    public function postSaveEquipment(Request $request)
    {

        $eq_arr = $request->get("equipments");
        $present_arr = $request->get("equipment-present");
        $working_arr = $request->get("equipment-working");
        $broken_arr = $request->get("equipment-broken");

        $report = Report::find($request->get("report_id"));
        $report->equipment()->detach();
        for ($i = 0; $i < sizeof($eq_arr); $i++) {
            if (!strlen($present_arr[$i]) == 0 || !strlen($working_arr[$i]) == 0 || !strlen($broken_arr[$i]) == 0) {
                $report->equipment()->attach($eq_arr[$i], [
                    "present" => $present_arr[$i],
                    "working" => $working_arr[$i],
                    "broken" => $broken_arr[$i],
                ]);
                Session::flash('flash_message', 'İlgili ekipman eklendi');
            }
        }
        return redirect()->back();
    }

    public function postDetachEquipment(Request $request)
    {
//        Main contractor
        $equipment_id = $request->get("equipmentid");
        $report = Report::find($request->get("report_id"));
        $report->equipment()->detach($equipment_id);
        return response()->json('success', 200);
    }


    public function postSaveWorkDone(Request $request)
    {
        $report = Report::find($request->get("report_id"));
        $subcontractor_ids = $request->get("subcontractors");
        $i = 0;
        if (!is_null($subcontractor_ids)) {
            foreach ($subcontractor_ids as $subcontractor_id) {
                $swunit = new Swunit();

                if (is_null($report->swunit()
                    ->where('subcontractor_id', $subcontractor_id)->first())
                ) {

                    $swunit = $swunit->create([
                        "subcontractor_id" => $subcontractor_id,
                        "report_id" => $report->id,
                        "quantity" => $request->get("subcontractor_quantity")[$i],
                        "unit" => $request->get("subcontractor_unit")[$i],
                        "works_done" => $request->get("subcontractor_work_done")[$i],
                        "planned" => $request->get("subcontractor_planned")[$i],
                        "done" => $request->get("subcontractor_done")[$i],
                    ]);
                } else {
                    $swunit = $report->swunit()
                        ->where('subcontractor_id', $subcontractor_id)->first();
                    $swunit->quantity = $request->get("subcontractor_quantity")[$i];
                    $swunit->unit = $request->get("subcontractor_unit")[$i];
                    $swunit->works_done = $request->get("subcontractor_work_done")[$i];
                    $swunit->planned = $request->get("subcontractor_planned")[$i];
                    $swunit->done = $request->get("subcontractor_done")[$i];
                    $swunit->save();
                }
                $i++;
                Session::flash('flash_message', 'İlgili çalışan birim eklendi');
            }
        }


        $staff_ids = $request->get("staffs");
        $i = 0;
        if (!is_null($staff_ids)) {
            foreach ($staff_ids as $staff_id) {
                $pwunit = new Pwunit();

                if (is_null($report->pwunit()->
                where('staff_id', $staff_id)->first())
                ) {

                    $pwunit = $pwunit->create([
                        "staff_id" => $staff_id,
                        "report_id" => $report->id,
                        "quantity" => $request->get("staff_quantity")[$i],
                        "unit" => $request->get("staff_unit")[$i],
                        "works_done" => $request->get("staff_work_done")[$i],
                        "planned" => $request->get("staff_planned")[$i],
                        "done" => $request->get("staff_done")[$i],
                    ]);
                } else {
                    $pwunit = $report->pwunit()->
                    where('staff_id', $staff_id)->first();
                    $pwunit->quantity = $request->get("staff_quantity")[$i];
                    $pwunit->unit = $request->get("staff_unit")[$i];
                    $pwunit->works_done = $request->get("staff_work_done")[$i];
                    $pwunit->planned = $request->get("staff_planned")[$i];
                    $pwunit->done = $request->get("staff_done")[$i];
                    $pwunit->save();
                }
                $i++;
                Session::flash('flash_message', 'İlgili çalışan birim eklendi');
            }
        }

        return redirect()->back();
    }

    public function postDeleteSwunit(Request $request)
    {
        Swunit::destroy($request->get('swid'));
        return response()->json('success', 200);
    }

    public function postDeletePwunit(Request $request)
    {
        Pwunit::destroy($request->get('pwid'));
        return response()->json('success', 200);
    }

    public function postSaveIncomingMaterial(Request $request)
    {
        $in_arr = $request->get("inmaterials");

        $report = Report::find($request->get("report_id"));
        $report_id = $report->id;
        $i = 0;
        foreach ($report->inmaterial()->get() as $inmat) {
            $inmat->delete();
        }
        foreach ($in_arr as $mat_id) {
            $inmaterial = new Inmaterial();

            if (empty($inmaterial->where("report_id", $report_id)
                ->where('material_id', $mat_id)->first())
            ) {
                if (!(empty($request->get("inmaterial-quantity")[$i]) &&
                    empty($request->get("inmaterial-unit")[$i]) &&
                    empty($request->get("inmaterial-from")[$i]) &&
                    empty($request->get("inmaterial-explanation")[$i]))
                ) {

                    $inmaterial = $inmaterial->create([
                        "material_id" => $mat_id,
                        "report_id" => $report_id,
                        "quantity" => $request->get("inmaterial-quantity")[$i],
                        "unit" => $request->get("inmaterial-unit")[$i],
                        "coming_from" => $request->get("inmaterial-from")[$i],
                        "explanation" => $request->get("inmaterial-explanation")[$i],
                    ]);
                }

            } else {

                $inmaterial = $inmaterial->where("report_id", $report_id)
                    ->where('material_id', $mat_id)->first();

                $inmaterial->quantity = $request->get("inmaterial-quantity")[$i];
                $inmaterial->unit = $request->get("inmaterial-unit")[$i];
                $inmaterial->coming_from = $request->get("inmaterial-from")[$i];
                $inmaterial->explanation = $request->get("inmaterial-explanation")[$i];
                $inmaterial->save();

            }
            $i++;
            Session::flash('flash_message', 'Gelen materyal tablosu güncellendi');
        }


        return redirect()->back();
    }

    public function postDeleteInmaterial(Request $request)
    {
        Inmaterial::find($request->get("inmaterialid"))->delete();
        return response()->json('success', 200);
    }

    public function postSaveOutgoingMaterial(Request $request)
    {
        $in_arr = $request->get("outmaterials");

        $report = Report::find($request->get("report_id"));
        $report_id = $report->id;
        $i = 0;
        foreach ($report->outmaterial()->get() as $inmat) {
            $inmat->delete();
        }
        foreach ($in_arr as $mat_id) {
            $outmaterial = new outmaterial();

            if (empty($outmaterial->where("report_id", $report_id)
                ->where('material_id', $mat_id)->first())
            ) {
                if (!(empty($request->get("outmaterial-quantity")[$i]) &&
                    empty($request->get("outmaterial-unit")[$i]) &&
                    empty($request->get("outmaterial-from")[$i]) &&
                    empty($request->get("outmaterial-explanation")[$i]))
                ) {

                    $outmaterial = $outmaterial->create([
                        "material_id" => $mat_id,
                        "report_id" => $report_id,
                        "quantity" => $request->get("outmaterial-quantity")[$i],
                        "unit" => $request->get("outmaterial-unit")[$i],
                        "coming_from" => $request->get("outmaterial-from")[$i],
                        "explanation" => $request->get("outmaterial-explanation")[$i],
                    ]);
                }

            } else {

                $outmaterial = $outmaterial->where("report_id", $report_id)
                    ->where('material_id', $mat_id)->first();

                $outmaterial->quantity = $request->get("outmaterial-quantity")[$i];
                $outmaterial->unit = $request->get("outmaterial-unit")[$i];
                $outmaterial->coming_from = $request->get("outmaterial-from")[$i];
                $outmaterial->explanation = $request->get("outmaterial-explanation")[$i];
                $outmaterial->save();

            }
            $i++;
            Session::flash('flash_message', 'Gelen materyal tablosu güncellendi');
        }


        return redirect()->back();
    }

    public function postDeleteOutmaterial(Request $request)
    {
        Outmaterial::find($request->get("outmaterialid"))->delete();
        return response()->json('success', 200);
    }

    public function postSelectIsWorking(Request $request)
    {
        $report = Report::find($request->get("report_id"));
        $report->is_working = $request->get("is_working");
        $report->save();
        Session::flash('flash_message', 'Şantiye çalışma durumu güncellendi');
        return redirect()->back();
    }

    public function postSaveFiles(Request $request)
    {
        $report = Report::find($request->get("report_id"));
        $db_file = $this->uploadFile($request->file("file"));

        if ($db_file) {
            if($request->get("type") == 0) {
                $photo = Photo::create();
                $report->photo()->save($photo);
                $photo->file()->save($db_file);
            }
            else{
                $receipt = Receipt::create();
                $report->receipt()->save($receipt);
                $receipt->file()->save($db_file);
            }
            return response()->json(['success' => 200,
                'id' => $db_file->id,
                'rid' => $report->id], 200);
        } else {
            return response()->json('error', 400);
        }

    }

    public function postDeleteFiles(Request $request)
    {
        $db_file = File::find($request->get("fileid"));

        $op_success = $db_file->fileable()->delete() && unlink($db_file->path . DIRECTORY_SEPARATOR . $db_file->name) && $db_file->delete();
        if ($op_success) {
            return response()->json('success', 200);
        } else {
            return response()->json('error', 400);
        }
    }

    public function patchLockReport(Request $request)
    {
        $report = Report::find($request->get("report_id"));
        $report->is_locked = $request->get("lock");
        $report->save();
        return redirect()->back();
    }

    public function postSaveShiftsMeals(Site $site, Request $request)
    {
        dd($request->all());
    }

//    END OF GUNLUK RAPOR PAGE


    //  TAŞERON CARİ HESAP PAGE AND RELATED OPERATIONS
    public function getAltYukleniciCariHesap(Site $site, Module $modules)
    {
        return view('tekil/subcontractor-account', compact('site', 'modules'));
    }

    public function getAltYukleniciDuzenle(Site $site, Module $modules, $id)
    {
        if (is_null($site->subcontractor()->find($id))) {
            return redirect()->back();
        }
        $subcontractor = Subcontractor::find($id);
        $costs = $subcontractor->cost()->additionalCosts(15);
        return view('tekil/subcontractor-edit', compact('subcontractor', 'site', 'modules', 'costs'));
    }

    public function postAddSubcontractor(Request $request, Site $site)
    {
        $subcontractor_ids = $request->get("subcontractors");
        foreach ($subcontractor_ids as $subcontractor_id) {
            if (!is_null($site->subcontractor()->onlyTrashed()->where('subcontractors.id', $subcontractor_id)->first())) {
                $site->subcontractor()->onlyTrashed()->where('subcontractors.id', $subcontractor_id)->first()->restore();
            } elseif (!$site->hasSubcontractor($subcontractor_id)) {
                Subcontractor::create([
                    'subdetail_id' => $subcontractor_id,
                    'site_id' => $site->id]);
            }
        }
        Session::flash('flash_message', "Alt yüklenici seçimleri güncellendi");
        return redirect()->back();

    }

    public function patchDelSubcontractor(Site $site)
    {
        $site->subcontractor()->delete();
        Session::flash('flash_message', "Taşeron kayıtları güncellendi");
        return redirect()->back();
    }

    public function postUpdateSubcontractor(Request $request)
    {
        $has_error = false;
        $subcontractor = Subcontractor::find($request->get('sub-id'));
        $sub_name = $subcontractor->name;
        $subcontractor->manufacturing()->detach();
        foreach ($request->get('manufacturings') as $man_id) {
            $subcontractor->manufacturing()->attach($man_id);
        }
        $subcontractor->price = $request->get('price');
        $subcontractor->save();
        $contract = Contract::firstOrCreate(['contract_date' => CarbonHelper::getMySQLDate($request->get('contract_date')),
            'contract_start_date' => CarbonHelper::getMySQLDate($request->get('contract_start_date')),
            'contract_end_date' => CarbonHelper::getMySQLDate($request->get('contract_end_date'))]);
        $subcontractor->contract()->save($contract);


        if ($request->file("contractToUpload")) {
            $file = $request->file("contractToUpload");

            if (!empty($contract->file)) {
                $db_file = $contract->file;

                if (unlink($db_file->path . DIRECTORY_SEPARATOR . $db_file->name)) {

                    $directory = $db_file->path;
                    $filename = $file->getClientOriginalName();

                    if ($file->move($directory, $filename)) {

                        $db_file->name = $filename;
                        $db_file->save();
                    } else {
                        $has_error = true;
                    }
                } else {
                    $has_error = true;
                }
            } else {
                $directory = public_path() . '/uploads/' . uniqid(rand(), true);
                $filename = $file->getClientOriginalName();

                $upload_success = $file->move($directory, $filename);

                if ($upload_success) {
                    $db_file = File::create([
                        "name" => $filename,
                        "path" => $directory
                    ]);

                    $contract->file()->save($db_file);
                } else {
                    $has_error = true;
                }
            }
        }
        if ($has_error) {
            Session::flash('flash_message_error', "Dosya yüklenirken hata oluştu");
        } else {
            Session::flash('flash_message', "Alt yüklenici ($sub_name) kaydı güncellendi");
        }
        return redirect()->back();
    }

    public function postUpdateFee(Request $request, Fee $fee)
    {
        $subcontractor = Subcontractor::find($request->get("subcontractor_id"));
        if (is_null($subcontractor->fee()->first())) {
            $fee->create($request->all());
        } else {
            $fee->breakfast = $request->get("breakfast");
            $fee->lunch = $request->get("lunch");
            $fee->supper = $request->get("supper");
            $fee->material = $request->get("material");
            $fee->equipment = $request->get("equipment");
            $fee->oil = $request->get("oil");
            $fee->cleaning = $request->get("cleaning");
            $fee->labour = $request->get("labour");
            $fee->shelter = $request->get("shelter");
            $fee->sgk = $request->get("sgk");
            $fee->allrisk = $request->get("allrisk");
            $fee->isg = $request->get("isg");
            $fee->contract_tax = $request->get("contract_tax");
            $fee->kdv = $request->get("kdv");
            $fee->electricity = $request->get("electricity");
            $fee->water = $request->get("water");
            $fee->save();
        }
        Session::flash('flash_message', 'Bilgiler kaydedildi');

        return redirect()->back();

    }

    public function postUpdateCost(Request $request)
    {
        $explain = $request->get("explanation");
        if (isset($explain) && strlen($explain) == 0) {
            Session::flash('flash_message_error', 'Ek ödemelerin açıklama kısmı boş olamaz');
            $this->validate($request, [
                "explanation" => "required"
            ]);
        }
        $my_arr = $request->all();
        $my_arr["pay_date"] = CarbonHelper::getMySQLDate($request->get("pay_date"));
        Cost::create($my_arr);
        Session::flash('flash_message', 'Bilgiler eklendi');

        return redirect()->back();
    }

    public function postSaveSubcontractorFiles(Request $request)
    {
        $subcontractor = Subcontractor::find($request->get('sub_id'));
        $db_file = $this->uploadFile($request->file("file"));

        if (!empty($db_file)) {
            $photo = Photo::create();
            $photo->file()->save($db_file);
            $subcontractor->photo()->save($photo);
        }


        if ($db_file && $photo) {
            return response()->json(['id' => $photo->id], 200);
        } else {
            return response()->json('error', 400);
        }
    }

    public function postDeleteSubcontractorFiles(Request $request)
    {
        Photo::find($request->get("fileid"))->delete();
        return response('success', 200);
    }


//  END OF TAŞERON CARİ HESAP PAGE


    public function getIsMakineleri(Site $site, Module $modules)
    {
        return view('tekil/equipments', compact('site', 'modules'));
    }

    public function postEditEquipments(Site $site, Request $request)
    {
        $site->equipment()->detach();

        foreach ($request->get("equipments") as $equipment) {
            $site->equipment()->attach($equipment);
        }

        Session::flash('flash_message', "Şantiye iş makineleri güncellendi");
        return redirect()->back();
    }

// START OF KASA PAGE
    public function getKasa(Site $site, Module $modules)
    {
        return view('tekil/account', compact('site', 'modules'));
    }

    public function postAddExpense(Request $request)
    {
        $this->validate($request, [
            'exp_date' => 'required',
            'account_id' => 'required',
            'buyer' => 'required',
            'definition' => 'required',
            'type' => 'required',
        ]);

        Expense::create([
            'exp_date' => CarbonHelper::getMySQLDate($request->get("exp_date")),
            'account_id' => $request->get("account_id"),
            'buyer' => $request->get("buyer"),
            'definition' => $request->get("definition"),
            'expense' => $request->get("expense"),
            'income' => $request->get("income"),
            'type' => $request->get("type"),
        ]);
        return response('success', 200);
    }

    public function getExpenses(Site $site)
    {

        $account = $site->account;
        $co = $account->card_owner;
        $expense_arr = [];
        foreach ($account->expense()->get() as $expense) {
            array_push($expense_arr, [
                'date' => CarbonHelper::getTurkishDate($expense->exp_date),
                'definition' => $expense->definition,
                'buyer' => $expense->buyer,
                'type' => $expense->type,
                'income' => $expense->income,
                'expense' => $expense->expense,
                'card_owner' => $co]);

        }


        return isset($expense_arr) ? response()->json($expense_arr, 200) : response()->json('error', 400);
    }

//    END OF KASA PAGE

    private function uploadFile($file)
    {
        $directory = public_path() . '/uploads/' . uniqid(rand(), true);
        $filename = $file->getClientOriginalName();

        if ($file->move($directory, $filename))

            return File::create([
                "name" => $filename,
                "path" => $directory
            ]);

        return null;
    }

}
