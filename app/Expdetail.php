<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expdetail extends Model
{
    use SoftDeletes;
    /*
     * Groups:
     * 1-> Genel
     * 2-> Sözleşme
     * 3-> Sarf
     * 4-> İnşaat Malzeme
     */

    protected $fillable = ['name', 'group'];
    protected $dates = ['deleted_at'];

    public function expenditure()
    {
        return $this->hasMany('App\Expenditure');
    }

    public function scopeOfGroup($query, $group)
    {
        return $query->where('group', '=', $group);
    }
}
