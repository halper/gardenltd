<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    //
    protected $table = 'accounts';

    protected $fillable = ['period', 'card_owner'];

    public function expense()
    {
        return $this->hasMany('App\Expense');
    }

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public function user()
    {
        return $this->belongsToMany('App\User')->withPivot('owner_type')->withTimestamps();
    }

}
