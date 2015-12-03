<?php

namespace App;

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Subcontractor extends Model implements SluggableInterface
{
    use SluggableTrait;

    protected $sluggable = [
        'build_from' => 'name',
        'save_to' => 'slug',
    ];

    protected $table = 'subcontractors';

    protected $fillable = ['name', 'address',
        'city_id', 'official', 'title', 'area_code_id', 'phone',
        'fax_code_id', 'fax', 'mobile_code_id', 'mobile', 'email',
        'web', 'tax_office', 'tax_number'];


    public function site()
    {
        return $this->belongsToMany('App\Site')->withPivot('contract_date', 'contract_start_date', 'contract_end_date')->withTimestamps();
    }

    public function report()
    {
        return $this->belongsToMany('App\Report', 'report_substaff')->withTimestamps();
    }

    public function manufacturing()
    {
        return $this->belongsToMany('App\Manufacturing')->withPivot('site_id')->join('sites', 'site_id', '=', 'sites.id');
    }

    public function hasManufacture($manufacture_id, $site_id)
    {
        return !is_null($this->manufacturing()->where('site_id', $site_id)->find($manufacture_id));
    }

    public function sfile()
    {
        return $this->hasOne('App\Sfile');
    }


}
