<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Substaff extends Model
{
    //
    protected $table = 'substaffs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    public function report()
    {
        return $this->belongsToMany('App\Report')->withPivot('quantity', 'subcontractor_id')->join('subcontractors', 'subcontractor_id', '=', 'subcontractors.id');
    }
}
