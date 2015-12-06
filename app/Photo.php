<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    //
    public function imageable()
    {
        return $this->morphTo();
    }

    public function file()
    {
        return $this->morphMany('App\File', 'fileable');
    }
}
