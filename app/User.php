<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\Permission;
use App\Site;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password', 'employer'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function site()
    {
        return $this->belongsToMany('App\Site')->withTimestamps();
    }

    public function permission()
    {
        return $this->belongsToMany('App\Permission')->withPivot('module_id')->join('modules', 'module_id', '=', 'modules.id');

    }

    public function isAdmin()
    {
        return $this->permission()->where('permission', '>=', '999')->count() >= 1;
    }

    public function canViewAllSites(){
        return $this->site()->where('sites.id', '99999')->count() >= 1;
    }

    public function hasSite($site_id){
        return ! is_null($this->site()->where('site_id', $site_id)->first());
    }

    public function hasPermissionOnModule($permission, $module){
        return ! is_null($this->permission()->where('permission_id', $permission)->where('module_id', $module)->first());
    }

    public function hasAnyPermissionOnModule($module)
    {
        return !is_null($this->permission()->where('module_id', $module)->first());
    }

    public function account()
    {
        return $this->belongsToMany('App\Account')->withPivot('owner_type')->withTimestamps();
    }

    public function report()
    {
        return $this->belongsToMany('App\Report')->withTimestamps();
    }

    public function scopeOwner($query)
    {
        return $query->where('owner_type', '=', '1');
    }
}
