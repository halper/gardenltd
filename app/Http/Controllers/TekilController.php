<?php

namespace App\Http\Controllers;

use App\Module;
use App\Site;
use App\Http\Requests;


class TekilController extends Controller
{
    //


    public function getIndex(Site $site, Module $modules)
    {
        return view('tekil/main', compact('site', 'modules'));
    }

    public function getGunlukRapor()
    {
        return redirect('/');
    }
}
