<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    //
    protected $fillable = ['name', 'planned', 'done', 'unit', 'report_id'];

    public function workable()
    {
        return $this->morphTo();
    }

    public function report()
    {
        return $this->belongsTo('App\Report');
    }
}
