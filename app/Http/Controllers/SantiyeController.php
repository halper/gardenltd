<?php

namespace App\Http\Controllers;

use App\City;
use App\Library\CarbonHelper;
use App\Library\TurkishChar;
use App\Site;
use App\Stock;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Session;

class SantiyeController extends ManagementController
{
    public function getIndex()
    {
        return view("landing/santiye");
    }

    public function postAdd(Request $request)
    {
        $this->validate($request, [
            'job_name' => 'required',
            'management_name' => 'required',
            'main_contractor' => 'required',
            'start_date' => 'required|date_format:d.m.Y',
            'contract_date' => 'required|date_format:d.m.Y',
            'end_date' => 'required|date_format:d.m.Y',
            'address' => 'required',
            'site_chief' => 'required',
            'employer' => 'required',
            'building_control' => 'required',
            'isg' => 'required',
            'city_id' => 'required',
        ]);


        $site = Site::create(
            [
                'job_name' => $request->get('job_name'),
                'management_name' => $request->get('management_name'),
                'main_contractor' => $request->get('main_contractor'),
                'start_date' => date("Y-m-d", strtotime($request->get('start_date'))),
                'contract_date' => date("Y-m-d", strtotime($request->get('contract_date'))),
                'end_date' => date("Y-m-d", strtotime($request->get('end_date'))),
                'address' => $request->get('address'),
                'site_chief' => $request->get('site_chief'),
                'employer' => $request->get('employer'),
                'building_control' => $request->get('building_control'),
                'isg' => $request->get('isg'),
                'contract_worth' => $request->get('contract_worth'),
                'extra_cost' => $request->get("extra_cost"),
            ]);
        $site->city()->associate(City::find($request->get("city_id")));
        Session::flash('flash_message', $request->get('job_name') . " şantiyesi eklendi");
        return redirect('santiye');
    }

    public function postUpdateSite(Request $request)
    {
        $this->validate($request, [
            'job_name' => 'required',
            'management_name' => 'required',
            'main_contractor' => 'required',
            'start_date' => 'required|date_format:d.m.Y',
            'contract_date' => 'required|date_format:d.m.Y',
            'end_date' => 'required|date_format:d.m.Y',
            'address' => 'required',
            'site_chief' => 'required',
            'employer' => 'required',
            'building_control' => 'required',
            'isg' => 'required',
            'city_id' => 'required',
        ]);
        $site = Site::find($request->id);
        $city = City::find($request->city_id);
        $site->job_name = $request->job_name;
        $site->management_name = $request->management_name;
        $site->main_contractor = $request->main_contractor;
        $site->start_date = CarbonHelper::getMySQLDate($request->start_date);
        $site->contract_date = CarbonHelper::getMySQLDate($request->contract_date);
        $site->end_date = CarbonHelper::getMySQLDate($request->end_date);
        $site->address = $request->address;
        $site->site_chief = $request->site_chief;
        $site->employer = $request->employer;
        $site->building_control = $request->building_control;
        $site->isg = $request->isg;
        $site->contract_worth = TurkishChar::convertCurrencyFromTr($request->contract_worth);
        $site->extra_cost = TurkishChar::convertCurrencyFromTr($request->extra_cost);
        $site->city()->associate($city);
        $site->save();
        Session::flash('flash_message', 'Kayıt güncellendi');
        return redirect()->back();
    }

    public function postDel(Request $request)
    {
        $this->validate($request, [
            'siteDeleteIn' => 'required'
        ]);

        $r_site = Site::find($request->get('siteDeleteIn'));
        Session::flash("flash_message", "<em>" . Site::find($request->get('siteDeleteIn'))->job_name . "</em> şantiyesi silindi");

        $r_site->delete();

        return redirect('santiye');

    }

    public function getStocks()
    {
        $resp_arr = [];
        $sites = Site::with('stock')->get();

        foreach ($sites as $site) {
            foreach ($site->stock()->get() as $stock) {
                array_push($resp_arr,
                    [
                        'site' => $site->job_name,
                        'st' => $stock->name,
                        'total' => $stock->total,
                        'amount' => $stock->pivot->amount,
                        'detail' => $stock->pivot->detail,
                        'unit' => $stock->unit,
                        'site_id' => $site->id,
                        'stock_id' => $stock->id
                    ]);
            }
        }
        return response($resp_arr, 200);
    }

    public function updateStock(Request $request)
    {
        $site = Site::find($request->site);
        $stock = $site->stock()->where('stock_id', $request->stock)->first();
        $stock->pivot->detail = $request->detail;
        $stock->pivot->save();
        return response('success', 200);
    }

    public function editSite(Site $site)
    {
        if (!\Auth::user()->isAdmin()) {
            return redirect('/santiye');
        }
        return view('landing.edit-site', compact('site'));
    }
}
