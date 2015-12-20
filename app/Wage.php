<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wage extends Model
{
    //

    protected $fillable = ['wage', 'since'];

    public function personnel()
    {
        return $this->belongsTo('App\Personnel');
    }

}
