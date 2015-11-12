<?php

namespace App\Http\Controllers;

use App\Demand;
use App\Material;
use App\Module;
use App\Report;
use App\Site;
use App\Http\Requests;
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

        if (is_null($report->where('created_at', Carbon::now()->toDateString())->first())) {
            $report->site_id = $site->id;
            $report->save();

        } else {
            $report = $report->where('created_at', Carbon::now()->toDateString())->where('site_id', $site->id)->first();
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
        $sql_date = Carbon::parse($report_date)->toDateString();
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
        $staff_arr = $request->get("staffs");
        $cont_arr = $request->get("contractor-quantity");

        $report = Report::find($request->get("report_id"));
        $report->staff()->detach();
        for ($i = 0; $i < sizeof($staff_arr); $i++) {
            if (!strlen($cont_arr[$i]) == 0) {
                $report->staff()->attach($staff_arr[$i], ["quantity" => $cont_arr[$i]]);
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


}
