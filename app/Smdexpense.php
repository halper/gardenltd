<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Smdexpense extends Model
{
    //
    protected $fillable = ['quantity', 'delivery_date', 'bill', 'detail'];

    public function submaterial()
    {
        return $this->belongsTo('App\Submaterial');
    }

    public function smdemand()
    {
        return $this->belongsTo('App\Smdemand');
    }
}
