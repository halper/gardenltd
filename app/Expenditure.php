<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expenditure extends Model
{
    /*
     * Types:
     * 1-> Genel
     * 2-> Sözleşme
     * 3-> Sarf
     * 4-> İnşaat Malzeme
     */
    protected $fillable = ['exp_date', 'amount', 'kdv', 'type', 'grand_total', 'explanation'];

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public function expdetail()
    {
        return $this->belongsTo('App\Expdetail');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', '=', $type);
    }
}
