<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personnel extends Model
{
    use SoftDeletes;
    //
    protected $table = 'personnel';

    protected $dates = ['deleted_at'];

    protected $fillable = ['name', 'tck_no', 'staff_id', 'iban'];

    public function personalize()
    {
        return $this->morphTo();
    }

    public function staff()
    {
        return $this->belongsTo('App\Staff');
    }

    public function photo()
    {
        return $this->morphMany('App\Photo', 'imageable');
    }

    public function meal()
    {
        return $this->hasMany('App\Meal');
    }

    public function shift()
    {
        return $this->hasMany('App\Shift');
    }

    public function scopeSitePersonnel($query)
    {
        return $query->wherePersonalizeType('App\Site');
    }

    public function scopeIsSitePersonnel($query)
    {
        return !empty($query->wherePersonalizeType('App\Site'));
    }

    public function scopeSubcontractorsPersonnel($query)
    {
        return $query->wherePersonalizeType('App\Subcontractor');
    }

    public function isSitePersonnel()
    {
        return strpos($this->personalize_type, 'App\Site') !== false;
    }

    public function contract()
    {
        return $this->morphOne('App\Contract', 'contractable');
    }

    public function wage()
    {
        return $this->hasMany('App\Wage');
    }

    public function iddoc()
    {
        return $this->hasOne('App\Iddoc');
    }

    public function salary()
    {
        return $this->hasMany('App\Salary');
    }

    public function site()
    {
        return $this->belongsToMany('App\Site')->withTimestamps();
    }

}
