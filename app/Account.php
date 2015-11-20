<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    //
    protected $table = 'accounts';

    protected $fillable = ['owner', 'period', 'init', 'site_id'];

    public function expense()
    {
        return $this->hasMany('App\Expense');
    }

    public function site()
    {
        return $this->belongsTo('App\Site');
    }
}
