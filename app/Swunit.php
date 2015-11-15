<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Swunit extends Model
{
    //
    protected $table = 'swunits';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['report_id', 'subcontractor_id', 'quantity', 'works_done', 'done', 'planned', 'unit'];

    public function report()
    {
        return $this->belongsTo('App\Report');
    }
}
