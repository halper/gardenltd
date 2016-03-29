<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    //
    protected $fillable = ['amount', 'date'];

    public function personnel()
    {
        return $this->belongsTo('App\Personnel');
    }

}
