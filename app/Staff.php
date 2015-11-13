<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staffs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['staff'];

    public function department()
    {
        return $this->belongsTo('App\Department');
    }
public function report()
    {
        return $this->belongsToMany('App\Report')->withPivot('quantity','subcontractor_id')->join('subcontractors', 'subcontractor_id', '=', 'subcontractors.id');
    }
}
