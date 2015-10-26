<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

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
        return $this->belongsToMany('User', 'users_permissions', 'permission_id', 'user_id')->withTimestamps();
    }
}
