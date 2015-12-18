<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    //
    protected $fillable = ['name', 'multiplier'];

    public function shift()
    {
        return $this->hasMany('App\Shift');
    }
}
