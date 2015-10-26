<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ManagementController extends Controller
{
    protected $data;

    function __construct()
    {
        $this->data = array();
        $this->middleware('auth');
    }
}
