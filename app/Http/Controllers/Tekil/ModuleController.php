<?php

namespace App\Http\Controllers\Tekil;

use App\Http\Controllers\ManagementController;
use App\Http\Requests;


class ModuleController extends ManagementController
{
    //

    public function getIndex()
    {
        return view("tekil/main");
    }

    public function getYemek()
    {
        return "What the heck";
    }
}
