<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Demand extends Model
{
    use SoftDeletes;
    protected $table = 'demands';
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['demand', 'details', 'firm', 'demand_date', 'approval_status'];

    public function materials()
    {
        return $this->belongsToMany('App\Material')->withPivot("quantity", "unit", "price", 'payment_type')->withTimestamps();
    }

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public function inmaterial()
    {
        return $this->hasMany('App\Inmaterial');
    }

    public function hasDelivered()
    {
        return !($this->inmaterial()->whereDate('inmaterials.created_at', '<', $this->attributes["demand_date"])->get()->isEmpty());
    }

    public function rejection()
    {
        return $this->hasOne('App\Rejection');
    }
}
