<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Iddoc extends Model
{
    //
    public function file()
    {
        return $this->morphMany('App\File', 'fileable');
    }

    public function personnel()
    {
        return $this->belongsTo('App\Personnel');
    }
}
