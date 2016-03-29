<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Outmaterial extends Model
{
    //
    protected $table = 'outmaterials';

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

    public function material()
    {
        return $this->belongsTo('App\Material');
    }
}
