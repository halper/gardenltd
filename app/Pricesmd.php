<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pricesmd extends Model
{
    //
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = ['since', 'price'];

    public function submaterial()
    {
        return $this->belongsTo('App\Submaterial');
    }

    public function smdemand()
    {
        return $this->belongsTo('App\Smdemand');
    }

    public function scopeOfSm($query, $id)
    {
        return $query->where('submaterial_id', $id)->orderBy('since', 'DESC');
    }

    public function scopeBeforeDD($query, $date)
    {
        return $query->where('since', '<', $date)->orderBy('since', 'DESC')->first();
    }
}
