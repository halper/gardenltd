<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instrument extends Model
{
    //
    protected $fillable = ['followup_date', 'firm', 'plate', 'fuel_stat', 'fuel',
    'work', 'unit', 'fee', 'total', 'detail'];

    public function equipment()
    {
        return $this->belongsTo('App\Equipment');
    }

    public function site()
    {
        return $this->belongsTo('App\Site');
    }
}
