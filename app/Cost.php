<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    //
    protected $table = 'costs';

    protected $fillable = ['breakfast', 'lunch', 'supper', 'material', 'equipment',
        'oil', 'cleaning', 'labour', 'pay_date', 'explanation', 'site_id', 'subcontractor_id'];

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public static function additionalCosts($site_id, $subcontractor_id, $paginate){
        return Cost::where('site_id', $site_id)->where('subcontractor_id', $subcontractor_id)->whereNotNull('explanation')
            ->orderBy('pay_date', 'DESC')->paginate($paginate);
    }
}
