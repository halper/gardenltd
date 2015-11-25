<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MobileCode extends Model
{
    //
    protected $table = 'mobile_codes';

    protected $fillable = ['code', 'country_id'];

    public $timestamps = false;
}
