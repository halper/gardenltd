<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submaterial extends Model
{
    //
    Use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = ['name', 'is_sm'];

    public function material()
    {
        return $this->belongsTo('App\Material');
    }

    public function smdemand()
    {
        return $this->belongsToMany('App\Smdemand')->withPivot('unit', 'quantity')->withTimestamps();
    }

    public function smdexpense()
    {
        return $this->hasMany('App\Smdexpense');
    }

    public function feature()
    {
        return $this->belongsToMany('App\Feature')->withTimestamps();
    }

    public function scopeBare($query)
    {
        return $query->where('is_sm', '1');
    }
}
