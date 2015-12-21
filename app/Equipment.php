<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    //
    use SoftDeletes;
    protected $table = "equipments";
    protected $dates = ['deleted_at'];
    protected $fillable = ['name'];

    public function site()
    {
        return $this->belongsToMany('App\Site')->withTimestamps();
    }

    public function report()
    {
        return $this->belongsToMany('App\Report')->withPivot('working', 'present', 'broken')->withTimestamps();
    }
}
