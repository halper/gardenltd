<?php

namespace App\Http\Controllers;


use App\Http\Requests;


class HomeController extends ManagementController
{
    public function getIndex()
    {
        return redirect("santiye");
    }


}
