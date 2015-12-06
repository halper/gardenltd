<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    //
    protected $fillable = ['report_id'];

    public function report()
    {
        return $this->belongsTo('App\Report');
    }

    public function file()
    {
        return $this->morphMany('App\File', 'fileable');
    }
}
