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
        return $this->belongsToMany('App\Demand')->withPivot("quantity", "unit");
    }

    public function request()
    {
        return $this->hasOne('App\Request');
    }
}
