<?php

namespace App\Http\Controllers;

use App\Demand;
use App\Library\CarbonHelper;
use App\Manufacturing;
use App\Material;
use App\Module;
use App\Report;
use App\Site;
use App\Http\Requests;
use App\Subcontractor;
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
        if(session()->has('report')){
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
                $report->staff()->attach($staff_arr[$i], ["quantity" => $cont_arr[$i], "subcontractor_id" => 1]);
            }
        }
        Session::flash('flash_message', 'İlgili personel eklendi');
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
        $report = Report::find($request->get("report_id"));
        $req_wo_token = $request->all();
        unset($req_wo_token["_token"]);
        unset($req_wo_token["subcontractors"]);
        unset($req_wo_token["report_id"]);
        $subcontractors = $request->get("subcontractors");
        for($i = 0; $i < sizeof($subcontractors); $i++){
            foreach($req_wo_token as $key => $value ){
//                key staff id
                if(!empty($req_wo_token[$key][$i]))
                    $report->staff()->attach($key, ["quantity" => $req_wo_token[$key][$i], "subcontractor_id" => $subcontractors[$i]]);
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

        foreach($request->get('manufacturings') as $man_id){
            Manufacturing::find($man_id)->subcontractor()->attach($subcontractor->id);
        }

        Session::flash('flash_message', "Taşeron ($subcontractor->name) kaydı oluşturuldu");
        return redirect()->back();

    }


}
