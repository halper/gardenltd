<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pwunit extends Model
{
    //
    protected $table = 'pwunits';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['report_id', 'staff_id', 'quantity', 'works_done', 'done', 'planned', 'unit'];

    public function report()
    {
        return $this->belongsTo('App\Report');
    }

}
