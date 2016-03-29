<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Labor extends Model
{
    //
    protected $fillable = ['detail', 'amount', 'lab_date'];

    public function site()
    {
        return $this->belongsTo('App\Site');
    }
}
