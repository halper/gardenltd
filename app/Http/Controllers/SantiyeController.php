<?php

namespace App\Http\Controllers;

use App\Site;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class SantiyeController extends ManagementController
{
    public function getIndex(){
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
        ]);



        Site::create(
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
            ]);
        Session::flash('flash_message', $request->get('job_name')." şantiyesi eklendi");
        return redirect('santiye');
    }

    public function postDel(Request $request)
    {
        $this->validate($request, [
            'siteDeleteIn' => 'required'
        ]);

        $r_site = Site::find($request->get('siteDeleteIn'));
        Session::flash("flash_message", "<em>". Site::find($request->get('siteDeleteIn'))->job_name."</em> şantiyesi silindi");

        $r_site->delete();

        return redirect('santiye');

    }
}
