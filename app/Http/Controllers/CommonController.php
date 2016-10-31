<?php

namespace App\Http\Controllers;

use App\Personnel;
use Illuminate\Http\Request;

use App\Http\Requests;

class CommonController extends ManagementController
{
    //

    public function postCheckTck(Request $request)
    {
        if (is_null(Personnel::withTrashed()->where('tck_no', $request->get('tck_no'))->first())) {
            return response()->json('unique', 200);
        } else {
            return response()->json('found!', 200);
        }

    }
}
