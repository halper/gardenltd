<?php

namespace App\Http\Controllers;

use App\Module;
use App\Site;
use App\Http\Requests;


class TekilController extends ManagementController
{
    //
    public function getSite(Site $site, Module $modules)
    {
        return view('tekil/main', compact('site', 'modules'));
    }

    public function getYemek()
    {
        return view('santiye');
    }
}
