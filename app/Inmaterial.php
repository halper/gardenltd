<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inmaterial extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['report_id', 'material_id', 'quantity', 'coming_from', 'explanation', 'unit', 'irsaliye'];

    public function report()
    {
        return $this->belongsTo('App\Report');
    }

    public function demand()
    {
        return $this->belongsTo('App\Demand');
    }

    public function material()
    {
        return $this->belongsTo('App\Material');
    }
}
