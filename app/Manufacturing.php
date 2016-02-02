<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Manufacturing extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'manufacturings';

    protected $fillable = ['name'];

    public function subcontractor()
    {
        return $this->belongsToMany('App\Subcontractor');
    }

}
