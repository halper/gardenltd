<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = "staffs";
    protected $fillable = ['staff', 'department_id'];


    public function department()
    {
        return $this->belongsTo('App\Department');
    }

    public function report()
    {
        return $this->belongsToMany('App\Report')->withPivot('quantity')->withTimestamps();
    }

    public function personnel()
    {
        return $this->hasMany('App\Personnel');
    }

    public function work()
    {
        return $this->morphMany('App\Work', 'workable');
    }
}
