<?php
namespace App;

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;
use App\User;

/**
 * Class Operation
 * @package App
 */
class Site extends Eloquent implements SluggableInterface
{
    use SluggableTrait;

    protected $sluggable = [
        'build_from' => 'job_name',
        'save_to' => 'slug',
    ];

    protected $table = 'sites';

    protected $fillable = ['job_name', 'management_name',
        'start_date', 'contract_date', 'main_contractor',
        'end_date', 'address', 'site_chief', 'employer', 'building_control',
    'isg'];

    public function user()
    {
        return $this->belongsToMany('App\User')->withTimestamps();
    }

    public function report()
    {
        return $this->hasMany('App\Report');
    }

    public function equipment()
    {
        return $this->belongsToMany('App\Equipment')->withTimestamps();
    }

    public function account()
    {
        return $this->hasOne('App\Account');
    }

    public function hasEquipment($eq_id)
    {
        return !is_null($this->equipment()->where('equipment_id', $eq_id)->first());
    }

    public function hasSubcontractor($id)
    {
        return !is_null($this->subcontractor()->where('subcontractor_id', $id)->first());
    }

    public function rfile()
    {
        return $this->hasMany('App\Rfile');
    }

    public function sfile()
    {
        return $this->hasMany('App\Sfile');
    }

    public function subcontractor()
    {
        return $this->belongsToMany('App\Subcontractor')->withPivot('contract_date', 'contract_start_date', 'contract_end_date', 'price')->withTimestamps();
    }

    public function fee()
    {
        return $this->hasMany('App\Fee');
    }

    public function cost()
    {
        return $this->hasMany('App\Cost');
    }

    public static function getSites()
    {
        return Site::where('id', '>', 1)->get();
    }

    public static function slugToId($slug)
    {
        return Site::where('slug', $slug)->first()->id;
    }
}