<?php

namespace App\Http\Controllers;

use App\Demand;
use App\Library\CarbonHelper;
use App\Manufacturing;
use App\Material;
use App\Module;
use App\Pwunit;
use App\Report;
use App\Site;
use App\Http\Requests;
use App\Subcontractor;
use App\Swunit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


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

    public function getKasa(Site $site, Module $modules)
    {
        return view('tekil/account', compact('site', 'modules'));
    }

    public function getTaseronCariHesap(Site $site, Module $modules)
    {
        return view('tekil/subcontractor-account', compact('site', 'modules'));
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


    public function postAddSubcontractor(Request $request, Site $site)
    {
        $this->validate($request, [
            'name' => 'required',
            'contract_date' => 'required',
            'contract_start_date' => 'required',
            'contract_end_date' => 'required',

        ]);

        $subcontractor = Subcontractor::create([
            "name" => $request->get("name"),
            'contract_date' => CarbonHelper::getMySQLDate($request->get("contract_date")),
            'contract_start_date' => CarbonHelper::getMySQLDate($request->get("contract_start_date")),
            'contract_end_date' => CarbonHelper::getMySQLDate($request->get("contract_end_date")),
            "site_id" => $site->id
        ]);

        foreach ($request->get('manufacturings') as $man_id) {
            Manufacturing::find($man_id)->subcontractor()->attach($subcontractor->id);
        }

        Session::flash('flash_message', "Taşeron ($subcontractor->name) kaydı oluşturuldu");
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


}
