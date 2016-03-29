<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    //
    protected $table = 'expenses';

    protected $fillable = ['exp_date', 'definition', 'buyer', 'type', 'income', 'expense', 'account_id'];

    public function account()
    {
        return $this->belongsTo('App\Account');
    }

    public function scopeDateRange($query, $start, $end)
    {
        return $query->where('exp_date', '>=', $start)->where('exp_date', '<=', $end);
    }
}
