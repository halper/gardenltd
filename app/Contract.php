<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    //

    protected $fillable = ['contract_date', 'contract_start_date', 'contract_end_date', 'exit_date'];

    public function file()
    {
        return $this->morphOne('App\File', 'fileable');
    }

    public function contractable()
    {
        return $this->morphTo();
    }

}
