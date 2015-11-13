<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manufacturing extends Model
{

    protected $table = 'manufacturings';

    protected $fillable = ['name'];

    public function subcontractor()
    {
        return $this->belongsToMany('App\Subcontractor')->where('subcontractor_id', '>', 1)->withTimestamps();
    }
    //
}
