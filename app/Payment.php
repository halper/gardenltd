<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
Use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = ['name', 'payment_date', 'amount', 'detail', 'method'];

    public function subcontractor()
    {
        return $this->belongsTo('App\Subcontractor');
    }
}
