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
        'save_to'    => 'slug',
    ];

    protected $table = 'sites';

    protected $fillable = ['job_name', 'management_name',
        'start_date', 'contract_date', 'main_contractor',
        'end_date', 'address', 'site_chief'];

     public function user()
    {
        return $this->belongsToMany('App\User')->withTimestamps();
    }

    public static function getSites()
    {
        return Site::where('id', '>', 1)->get();
    }

    public static function slugToId($slug){
        return Site::where('slug', $slug)->first()->id;
    }
}