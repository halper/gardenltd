<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    //
    protected $fillable = ['no', 'allowance_date', 'amount', 'detail'];

    public function site()
    {
        return $this->belongsTo('App\Site');
    }
}
