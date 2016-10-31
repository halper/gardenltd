<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;
    //
    protected $fillable = ['name'];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsToMany('App\User')->withTimestamps();
    }

    public function permission()
    {
        return $this->belongsToMany('App\Permission')->withPivot('module_id')->join('modules', 'module_id', '=', 'modules.id');
    }

    public function site()
    {
        return $this->belongsToMany('App\Site')->withTimestamps();
    }

    public function hasSite($site_id)
    {
        return !is_null($this->site()->where('site_id', $site_id)->first());
    }

    public function hasUser($user_id)
    {
        return !is_null($this->user()->where('user_id', $user_id)->first());
    }

    public function hasPermissionOnModule($permission, $module)
    {
        return !is_null($this->permission()->where('permission_id', $permission)->where('module_id', $module)->first());
    }

    public function hasAnyPermissionOnModule($module)
    {
        return !is_null($this->permission()->where('module_id', $module)->first());
    }

    public function specialPermission()
    {
        return $this->belongsToMany('App\SpecialPermission');
    }

    public function hasSpecialPermission($id)
    {
        return !is_null($this->specialPermission()->where('special_permission_id', $id)->first());
    }

    public function hasSpecialPermissionForSlug($slug)
    {
        return !is_null($this->specialPermission()->where('slug', $slug)->first());
    }
}
