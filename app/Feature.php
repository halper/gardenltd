<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feature extends Model
{
    //
    Use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = ['name', 'is_sm'];

    public function material()
    {
        return $this->belongsTo('App\Material');
    }

    public function submaterial()
    {
        return $this->belongsToMany('App\Submaterial')->withTimestamps();
    }
}
