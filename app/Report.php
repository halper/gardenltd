<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['site_id', 'management_staff', 'employer_staff', 'building_control_staff',
        'weather', 'temp_min', 'temp_max', 'humidity', 'wind', 'is_working', 'admin_lock'];

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public function staff()
    {
        return $this->belongsToMany('App\Staff')->withPivot('quantity')->withTimestamps();
    }

    public function substaff()
    {
        return $this->belongsToMany('App\Substaff')->withPivot('quantity', 'subcontractor_id')->join('subcontractors', 'subcontractor_id', '=', 'subcontractors.id');
    }

    public function equipment()
    {
        return $this->belongsToMany('App\Equipment')->withPivot('working', 'present', 'broken')->withTimestamps();
    }

    public function pwunit()
    {
        return $this->hasMany('App\Pwunit');
    }

    public function swunit()
    {
        return $this->hasMany('App\Swunit');
    }

    public function inmaterial()
    {
        return $this->hasMany('App\Inmaterial');
    }

    public function rfile()
    {
        return $this->hasMany('App\Rfile');
    }

    public function locked()
    {
        return $this->is_locked == 1;
    }


}
