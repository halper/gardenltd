<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    //
    protected $table = 'costs';

    protected $fillable = ['material', 'equipment',
        'oil', 'cleaning', 'labour', 'pay_date', 'explanation', 'subcontractor_id'];

    public function subcontractor()
    {
        return $this->belongsTo('App\Subcontractor');
    }

    public static function scopeAdditionalCosts($query, $paginate){
        return $query->whereNotNull('explanation')
            ->orderBy('pay_date', 'DESC')->paginate($paginate);
    }
}
