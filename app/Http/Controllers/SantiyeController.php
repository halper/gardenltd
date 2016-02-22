<?php

namespace App\Http\Controllers;

use App\Account;
use App\City;
use App\Library\CarbonHelper;
use App\Library\TurkishChar;
use App\Site;
use App\Stock;
use App\User;
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
            'code' => 'required',
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
                'code' => $request->get('code'),
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
        $account = new Account();
        $account->site()->associate($site);
        $account->save();
        $site->city()->associate(City::find($request->get("city_id")));
        Session::flash('flash_message', $request->get('job_name') . " şantiyesi eklendi");
        return redirect('santiye');
    }

    public function postUpdateSite(Request $request)
    {
        $this->validate($request, [
            'job_name' => 'required',
            'code' => 'required',
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
        $site->code = $request->code;
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

    public function getRetrieveItems()
    {
        $resp_arr = [];
        $sites = Site::with('stock')->get();
        $stocks = Stock::all();

        foreach ($stocks as $stock) {
            $left = $stock->total;
            foreach ($sites as $site) {
                if (!empty($site->stock()->where('stocks.id', '=', $stock->id)->first()))
                    $left -= $site->stock()->where('stocks.id', '=', $stock->id)->first()->pivot->amount;
            }
            array_push($resp_arr, [
                'name' => $stock->name,
                'total' => $stock->total,
                'unit' => TurkishChar::tr_up($stock->unit),
                'left' => $left
            ]);
        }
        return response($resp_arr, 200);
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

    public function getSites()
    {
        $resp_arr = [];
        foreach (Site::getSites() as $site) {
            array_push($resp_arr, [
                'jobName' => $site->job_name,
                'id' => $site->id
            ]);
        }

        return response()->json($resp_arr, 200);
    }

    public function getUsers()
    {
        $resp_arr = [];
        foreach (User::all() as $user) {
            array_push($resp_arr, [
                'name' => $user->name,
                'id' => $user->id
            ]);
        }

        return response()->json($resp_arr, 200);
    }

    public function getReports()
    {
        $resp_arr = [];
        foreach (User::all() as $user) {
            foreach ($user->report()->get() as $report) {
                array_push($resp_arr, [
                    'date' => CarbonHelper::getTurkishDate($report->created_at),
                    'site' => $report->site->job_name,
                    'uid' => $user->id,
                    'user' => $user->name,
                    'id' => $report->id
                ]);
            }
        }

        return response()->json($resp_arr, 200);
    }

    public function postAccountDetails(Request $request)
    {
        $site = Site::find($request->id);
        $site->load('account');
        return response()->json([
            "id" => $site->account->id,
            "owner" => $site->account->user()->get()->isEmpty() ? "" : $site->account->user()->owner()->first()->name,
            "uid" => $site->account->user()->get()->isEmpty() ? "" : $site->account->user()->owner()->first()->id,
            "cardOwner" => empty($site->account->card_owner) ? "" : $site->account->card_owner,
            "period" => empty($site->account->period) ? "" : $site->account->period
        ], 200);
    }

    public function postSaveAccount(Request $request)
    {
        $acc = Account::find($request->id);
        $acc->card_owner = $request->card_owner;
        $acc->period = $request->period;
        $acc->user()->detach();
        $acc->user()->attach(User::find($request->uid), ['owner_type' => 1]);
        $acc->save();
        return response('success', 200);
    }
}
