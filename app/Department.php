<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = ['department'];

    public function staff()
    {
        return $this->hasMany('App\Staff');
    }

    public function management()
    {
        return $this->where('department', '!=', 'Taşeron')->get();
    }
}
