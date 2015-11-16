<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inmaterial extends Model
{
    //
    protected $table = 'inmaterials';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['report_id', 'material_id', 'quantity', 'coming_from', 'explanation', 'unit'];

    public function report()
    {
        return $this->belongsTo('App\Report');
    }
}
