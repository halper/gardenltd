<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    //
    protected $table = 'fees';

    protected $fillable = ['breakfast', 'lunch', 'supper', 'material',
        'equipment', 'oil', 'cleaning', 'labour', 'shelter', 'sgk', 'allrisk',
        'isg', 'contract_tax', 'kdv', 'electricity', 'water', 'subcontractor_id'];

    public function subcontractor()
    {
        return $this->belongsTo('App\Subcontractor');
    }
}
