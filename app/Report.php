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
    'weather', 'temp_min', 'temp_max', 'humidity', 'wind', 'is_working'];

    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public function staff()
    {
        return $this->belongsToMany('App\Staff')->withPivot('quantity')->withTimestamps();
    }

    public function locked()
    {
        return $this->is_locked == 1;
    }


}
