<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['material'];

    public function demands()
    {
        return $this->belongsToMany('App\Demand')->withPivot("quantity", "unit");;
    }

    public function request()
    {
        return $this->hasOne('App\Request');
    }
}
