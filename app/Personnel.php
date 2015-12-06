<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personnel extends Model
{
    use SoftDeletes;
    //
    protected $table = 'personnel';

    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'tck_no', 'staff_id'];

    public function personalize()
    {
        return $this->morphTo();
    }

    public function staff()
    {
        return $this->belongsTo('App\Staff');
    }

    public function photo()
    {
        return $this->morphMany('App\Report', 'imageable');
    }



}
