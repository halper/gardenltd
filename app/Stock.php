<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    //
    Use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'total', 'unit'];

    public function site()
    {
        return $this->belongsToMany('App\Site')->withPivot('amount', 'detail')->withTimestamps();
    }
}
