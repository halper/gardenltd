<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AreaCode extends Model
{
    //
    protected $table = 'area_codes';

    protected $fillable = ['code', 'country_id'];

    public $timestamps = false;
}

