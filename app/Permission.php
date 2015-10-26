<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    //
    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['permission', 'definition'];

    public function user()
    {
        return $this->belongsToMany('App\User')->withTimestamps();
    }
}
