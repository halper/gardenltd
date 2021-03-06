<?php

namespace App;

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Subcontractor extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = ['price', 'subdetail_id', 'site_id', 'additional_bid_cost', 'kdv'];

    public function subdetail()
    {
        return $this->belongsTo('App\Subdetail');
    }

    public function site()
    {
        return $this->belongsTo('app\Site');
    }

    public function report()
    {
        return $this->belongsToMany('App\Report', 'report_substaff')->withTimestamps();
    }

    public function manufacturing()
    {
        return $this->belongsToMany('App\Manufacturing')->withTimestamps();
    }

    public function hasManufacture($manufacture_id)
    {
        return !is_null($this->manufacturing()->find($manufacture_id));
    }

    public function work()
    {
        return $this->morphMany('App\Work', 'workable');
    }

    public function contract()
    {
        return $this->morphOne('App\Contract', 'contractable');
    }

    public function photo()
    {
        return $this->morphMany('App\Photo', 'imageable');
    }

    public function personnel()
    {
        return $this->morphMany('App\Personnel', 'personalize');
    }

    public function fee()
    {
        return $this->hasMany('App\Fee');
    }

    public function payment()
    {
        return $this->hasMany('App\Payment');
    }

}
