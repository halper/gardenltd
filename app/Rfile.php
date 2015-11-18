<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rfile extends Model
{
    //
    protected $table = 'rfiles';

    protected $fillable = ['report_id', 'file_id', 'site_id'];

    public function file()
    {
        return $this->belongsTo('App\File');
    }

    public function report()
    {
        return $this->belongsTo('App\Report');
    }

    public function site()
    {
        return $this->belongsTo('App\site');
    }
}
