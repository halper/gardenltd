<?php

namespace App\Http\Controllers\Tekil;

use App\Http\Controllers\Controller;
use App\Http\Requests;


class ModuleController extends Controller
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
