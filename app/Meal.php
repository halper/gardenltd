<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    //
    protected $fillable = ['meal', 'site_id', 'report_id', 'personnel_id'];

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

    public function scopeOfReport($query, $rid)
    {
        return $query->where('report_id', '=', $rid);
    }

    public function scopeOfPersonnel($query, $pid)
    {
        return $query->where('report_id', '=', $pid);
    }
}
