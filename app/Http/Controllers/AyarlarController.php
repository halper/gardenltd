<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class AyarlarController extends ManagementController
{
    //
    protected $data;

    function __construct()
    {
        $this->data = array();
        $this->middleware('admin');
    }

    public function getIndex()
    {
        $users = User::all();
        return 'Admin clearance has provided';
//        return view('landing/ayarlar', compact('users'));
    }
}
