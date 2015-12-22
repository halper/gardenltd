<?php

namespace App\Http\Controllers;


use App\Http\Requests;


class HomeController extends ManagementController
{
    public function getIndex()
    {
        return redirect("santiye");
    }

    public function getUploads($directory, $filename)
    {
        $file = public_path(). "/uploads/$directory/$filename";
        return response()->download($file, $filename);
    }


}
