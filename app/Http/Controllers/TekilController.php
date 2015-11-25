<?php

namespace App\Http\Controllers;

use App\Account;
use App\Demand;
use App\Expense;
use App\File;
use App\Inmaterial;
use App\Library\CarbonHelper;
use App\Manufacturing;
use App\Material;
use App\Module;
use App\Pwunit;
use App\Report;
use App\Rfile;
use App\Sfile;
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

    public function postAddManagementStaffs(Site $site, Request $request)
    {
        $report = Report::where('created_at', Carbon::now()->toDateString())
            ->where('site_id', $site->id)->first();
        $exists = false;
        if (!strlen($request->get('employer_staff')) == 0) {
            $report->employer_staff = $request->get('employer_staff');
            $exists = true;
        }
        if (!strlen($request->get('management_staff')) == 0) {
            $report->management_staff = $request->get('management_staff');
            $exists = true;
        }
        if (!strlen($request->get('building_control_staff')) == 0) {
            $report->building_control_staff = $request->get('building_control_staff');
            $exists = true;
        }
        if ($exists) {
            $report->save();
        }
        Session::flash('flash_message', 'Personel icmal kaydı başarılı');
        return redirect()->back();
    }

    public function postSaveSubcontractorStaff(Request $request)
    {
//        Daily report subcontractor staff

        $report = Report::find($request->get("report_id"));
        $req_wo_token = $request->all();
        unset($req_wo_token["_token"]);
        unset($req_wo_token["subcontractors"]);
        unset($req_wo_token["report_id"]);
        $subcontractors = $request->get("subcontractors");
        $report->substaff()->detach();
        for ($i = 0; $i < sizeof($subcontractors); $i++) {
            foreach ($req_wo_token as $key => $value) {
//                key staff id
                if (!empty($req_wo_token[$key][$i]))
                    $report->substaff()->attach($key, ["quantity" => $req_wo_token[$key][$i], "subcontractor_id" => $subcontractors[$i]]);
            }
        }
        Session::flash('flash_message', 'Taşeron personel kaydı başarılı');
        return redirect()->back();
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

    public function postSaveWorkDone(Request $request)
    {
        $report_id = $request->get("report_id");
        $subcontractor_ids = $request->get("subcontractors");
        $i = 0;
        foreach ($subcontractor_ids as $subcontractor_id) {
            $swunit = new Swunit();

            if (empty($swunit->where("report_id", $report_id)
                ->where('subcontractor_id', $subcontractor_id)->first())
            ) {

                $swunit = $swunit->create([
                    "subcontractor_id" => $subcontractor_id,
                    "report_id" => $report_id,
                    "quantity" => $request->get("subcontractor_quantity")[$i],
                    "unit" => $request->get("subcontractor_unit")[$i],
                    "works_done" => $request->get("subcontractor_work_done")[$i],
                    "planned" => $request->get("subcontractor_planned")[$i],
                    "done" => $request->get("subcontractor_done")[$i],
                ]);
            } else {
                $swunit = $swunit->where("report_id", $report_id)
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


        $staff_ids = $request->get("staffs");
        $i = 0;
        foreach ($staff_ids as $staff_id) {
            $pwunit = new Pwunit();

            if (empty($pwunit->where("report_id", $report_id)
                ->where('staff_id', $staff_id)->first())
            ) {

                $pwunit = $pwunit->create([
                    "staff_id" => $staff_id,
                    "report_id" => $report_id,
                    "quantity" => $request->get("staff_quantity")[$i],
                    "unit" => $request->get("staff_unit")[$i],
                    "works_done" => $request->get("staff_work_done")[$i],
                    "planned" => $request->get("staff_planned")[$i],
                    "done" => $request->get("staff_done")[$i],
                ]);
            } else {
                $pwunit = $pwunit->where("report_id", $report_id)
                    ->where('staff_id', $staff_id)->first();
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

        return redirect()->back();
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

    public function postSelectIsWorking(Request $request)
    {
        $report = Report::find($request->get("report_id"));
        $report->is_working = $request->get("is_working");
        $report->save();
        Session::flash('flash_message', 'Şantiye çalışma durumu güncellendi');
        return redirect()->back();
    }

    public function postSaveFiles(Request $request, Site $site)
    {
        $my_file_type = $request->get("type");
        $report = Report::find($request->get("report_id"));
        $file = $request->file("file");

        $directory = public_path() . '/uploads/' . uniqid(rand(), true);
        $filename = $file->getClientOriginalName();

        $upload_success = $file->move($directory, $filename);


        $db_file = File::create([
            "name" => $filename,
            "path" => $directory,
            "type" => $my_file_type,
        ]);

        $rfile = Rfile::create([
            "site_id" => $site->id,
            "file_id" => $db_file->id,
            "report_id" => $report->id
        ]);

        if ($upload_success && $db_file && $rfile) {
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

        $op_success = unlink($db_file->path . DIRECTORY_SEPARATOR . $db_file->name) && Report::find($request->get("reportid"))->rfile()->get()->where("file_id", $db_file->id)->first()->delete();
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

//    END OF GUNLUK RAPOR PAGE


    //  TAŞERON CARİ HESAP PAGE AND RELATED OPERATIONS
    public function getTaseronCariHesap(Site $site, Module $modules)
    {
        return view('tekil/subcontractor-account', compact('site', 'modules'));
    }

    public function getTaseronDuzenle(Site $site, Module $modules, $id)
    {
        $subcontractor = Subcontractor::find($id);
        return view('tekil/subcontractor-edit', compact('subcontractor', 'site', 'modules'));
    }

    public function postAddSubcontractor(Request $request, Site $site)
    {

        $site->subcontractor()->detach();

        foreach ($request->get("subcontractors") as $subcontractor) {
            $site->subcontractor()->attach($subcontractor);
        }

        Session::flash('flash_message', "Taşeron seçimleri güncellendi");
        return redirect()->back();

    }

    public function postUpdateSubcontractor(Site $site, Request $request)
    {
        $has_error = false;
        $subcontractor = Subcontractor::find($request->get('sub-id'));
        $subcontractor->manufacturing()->detach();
        foreach ($request->get('manufacturings') as $man_id) {
            $subcontractor->manufacturing()->attach($man_id);
        }
        $subcontractor->name = $request->get('name');
        $subcontractor->contract_date = CarbonHelper::getMySQLDate($request->get('contract_date'));
        $subcontractor->contract_start_date = CarbonHelper::getMySQLDate($request->get('contract_start_date'));
        $subcontractor->contract_end_date = CarbonHelper::getMySQLDate($request->get('contract_end_date'));

        if ($request->file("contractToUpload")) {
            $file = $request->file("contractToUpload");

            if (!empty($subcontractor->sfile)) {
                $db_file = $subcontractor->sfile->file;

                if (unlink($db_file->path . DIRECTORY_SEPARATOR . $db_file->name)) {

                    $directory = $db_file->path;
                    $filename = $file->getClientOriginalName();

                    if ($file->move($directory, $filename)) {

                        $db_file->name = $filename;
                        $db_file->save();
                    }
                    else{
                        $has_error = true;
                    }
                }
                else{
                    $has_error = true;
                }
            } else {
                $directory = public_path() . '/uploads/' . uniqid(rand(), true);
                $filename = $file->getClientOriginalName();

                $upload_success = $file->move($directory, $filename);

                if ($upload_success) {
                    $db_file = File::create([
                        "name" => $filename,
                        "path" => $directory,
                        "type" => 2,
                    ]);

                    $sfile = Sfile::create([
                        "file_id" => $db_file->id,
                        "subcontractor_id" => $subcontractor->id

                    ]);
                }
                else{
                    $has_error = true;
                }
            }
        }
        if($has_error){
            Session::flash('flash_message_error', "Dosya yüklenirken hata oluştu");
        }
        else {
            Session::flash('flash_message', "Taşeron ($subcontractor->name) kaydı güncellendi");
        }
            return redirect()->back();
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

}
