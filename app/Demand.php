<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Demand extends Model
{
    protected $table = 'demands';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['demand'];

    public function materials()
    {
        $this->belongsToMany('App\Material')->withPivot("quantity", "unit");
    }
}
