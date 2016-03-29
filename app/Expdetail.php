<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expdetail extends Model
{
    /*
     * Groups:
     * 1-> Genel
     * 2-> Sözleşme
     * 3-> Sarf
     * 4-> İnşaat Malzeme
     */

    protected $fillable = ['name', 'group'];

    public function expenditure()
    {
        return $this->hasMany('App\Expenditure');
    }

    public function scopeOfGroup($query, $group)
    {
        return $query->where('group', '=', $group);
    }
}
