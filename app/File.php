<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    //
    protected $table = 'files';

    protected $fillable = ['name', 'path'];

    public function rfile()
    {
        return $this->hasMany('App\Rfile');
    }

    public function sfile()
    {
        return $this->hasMany('App\Sfile');
    }


}
