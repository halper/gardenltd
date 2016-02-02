<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mealcost extends Model
{
    //
    protected $fillable = ['breakfast', 'lunch', 'supper', 'since'];

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public function scopeSinceDate($query, $date)
    {
        return $query->where('since', '<=', $date)->orderBy('since', 'DESC');
    }
}
