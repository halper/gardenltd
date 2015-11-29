<?php

namespace App;

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsToMany('App\Site')->withTimestamps();
    }

    public function report()
    {
        return $this->belongsToMany('App\Report')->withTimestamps();
    }

    public function manufacturing()
    {
        return $this->belongsToMany('App\Manufacturing')->withTimestamps();
    }

    public function hasManufacture($manufacture_id)
    {
        return !is_null($this->manufacturing()->find($manufacture_id));
    }

    public function sfile()
    {
        return $this->hasOne('App\Sfile');
    }


}
