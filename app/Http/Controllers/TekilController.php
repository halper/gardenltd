<?php

namespace App\Http\Controllers;

use App\Demand;
use App\Material;
use App\Module;
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
        return view('tekil/daily', compact('site', 'modules'));
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
        }
        else
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
        foreach($request->get("materials") as $mat){
            $my_mat = Material::find($mat);
            $my_mat->demands()->attach($demand->id, ["quantity" => $request->get("quantity")[$mat-1],
                "unit" => $request->get("unit")[$mat-1],]);
        }
        Session::flash('flash_message', 'Malzeme talep formu oluşturuldu');
        return redirect()->back();
    }

    public function postSelectDate(Request $request)
    {
        $data = ["date" => $request->get("date")];
        return redirect()->back()->with("data", $data);
    }

    public function postAddStaffs(Request $request)
    {
        dd($request->all());
        $staff_array = $request->get("staffs");
        return redirect()->back()->with("staff_array", $staff_array);
    }

    public function postSaveStaff(Request $request){
        $staff_array = $request->get("staffs");
        $quantity = $request->get("quantity");
        Session::flash('flash_message', 'Personel kaydı oluşturuldu');
        return redirect()->back()->with("staff_array", $staff_array)
            ->with("quantity_array", $quantity);
    }


}
