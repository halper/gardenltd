<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = ['material'];

    public function demands()
    {
        return $this->belongsToMany('App\Demand')->withPivot("quantity", "unit", "price", 'payment_type')->withTimestamps();
    }

    public function request()
    {
        return $this->hasOne('App\Request');
    }

    public function inmaterial()
    {
        return $this->hasMany('App\Inmaterial');
    }

    public function outmaterial()
    {
        return $this->hasMany('App\Outmaterial');
    }

    public function hasDemanded($id)
    {
        return !is_null($this->inmaterial()->where('demand_id', $id)->first());
    }

    public function submaterial()
    {
        return $this->hasMany('App\Submaterial');
    }

    public function feature()
    {
        return $this->hasMany('App\Feature');
    }

    public function smdemand()
    {
        return $this->hasMany('App\Smdemand');
    }

}
