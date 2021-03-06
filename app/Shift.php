<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    //
    protected $fillable = ['overtime', 'site_id', 'report_id', 'personnel_id', 'overtime_id'];

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public function personnel()
    {
        return $this->belongsTo('App\Personnel');
    }

    public function report()
    {
        return $this->belongsTo('App\Report');
    }

    public function overtime()
    {
        return $this->belongsTo('App\Overtime');
    }

    public function scopeOfReport($query, $rid)
    {
        return $query->where('report_id', '=', $rid);
    }

    public function scopeOfPersonnel($query, $pid)
    {
        return $query->where('report_id', '=', $pid);
    }
}
