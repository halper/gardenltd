<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sfile extends Model
{
    //
    protected $table = 'sfiles';

    protected $fillable = ['subcontractor_id', 'file_id', 'site_id'];

    public function file()
    {
        return $this->belongsTo('App\File');
    }

    public function subcontractor()
    {
        return $this->belongsTo('App\Subcontractor');
    }

    public function site()
    {
        return $this->belongsTo('App\site');
    }
}
