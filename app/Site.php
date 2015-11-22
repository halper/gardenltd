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
        'end_date', 'address', 'site_chief', 'employer', 'building_control'];

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

    public function account(){
        return $this->hasOne('App\Account');
    }

    public function hasEquipment($eq_id){
        return ! is_null($this->equipment()->where('equipment_id', $eq_id)->first());
    }

    public function rfile()
    {
        return $this->hasMany('App\Rfile');
    }

    public function sfile()
    {
        return $this->hasMany('App\Sfile');
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