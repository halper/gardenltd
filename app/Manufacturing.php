<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Manufacturing extends Model
{

    protected $table = 'manufacturings';

    protected $fillable = ['name'];

    public function subcontractor()
    {
        return $this->belongsToMany('App\Subcontractor');
    }

}
