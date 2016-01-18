<?php

namespace App;

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subdetail extends Model implements SluggableInterface
{
    use SoftDeletes;
    use SluggableTrait;

    protected $sluggable = [
        'build_from' => 'name',
        'save_to' => 'slug',
    ];

    protected $fillable = ['name', 'address',
        'city_id', 'official', 'title', 'area_code_id', 'phone',
        'fax_code_id', 'fax', 'mobile_code_id', 'mobile', 'email',
        'web', 'tax_office', 'tax_number'];

    protected $dates = ['deleted_at'];


    public function subcontractor()
    {
        return $this->hasMany('App\Subcontractor');
    }

    public function mobilecode()
    {
        return $this->belongsTo('App\MobileCode', 'mobile_code_id', 'id');
    }

    public function areacode()
    {
        return $this->belongsTo('App\AreaCode', 'area_code_id', 'id');
    }

    public function faxcode()
    {
        return $this->belongsTo('App\AreaCode', 'area_code_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo('App\City');
    }
}
