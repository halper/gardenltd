<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    //
    protected $table = 'equipments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
