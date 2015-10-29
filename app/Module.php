<?php
namespace App;

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
* Class Operation
* @package App
*/
class Module extends Eloquent implements SluggableInterface
{
    use SluggableTrait;

    protected $sluggable = [
        'build_from' => 'name',
        'save_to'    => 'slug',
    ];

    protected $table = 'modules';

    protected $fillable = ['name', 'icon'];

    public static function getModules(){
        return Module::where('id', '>', 1)->get();
    }

}