<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    //
    protected $fillable = ['overtime'];

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public function personnel()
    {
        return $this->belongsTo('App\Personnel');
    }

    public function report()
    {
        return $this->belongsTo('App\Report');
    }
}
