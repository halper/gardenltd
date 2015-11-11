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
    protected $fillable = ['site_id, management_staff,employer_staff,building_control_staff'];

    public function site()
    {
        return $this->belongsTo('App\Site');
    }
}
