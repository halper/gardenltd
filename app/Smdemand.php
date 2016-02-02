<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Smdemand extends Model
{
    //
    Use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = ['contract_cost'];

    public function submaterial()
    {
        return $this->belongsToMany('App\Submaterial')->withPivot('unit', 'quantity');
    }

    public function smdexpense()
    {
        return $this->hasMany('App\Smdexpense');
    }

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public function material()
    {
        return $this->belongsTo('App\Material');
    }

    public function pricesmd()
    {
        return $this->hasMany('App\Pricesmd');
    }
}
