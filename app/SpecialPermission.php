<?php

namespace App;

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Illuminate\Database\Eloquent\Model;

class SpecialPermission extends Model implements SluggableInterface
{
    use SluggableTrait;
    //

    protected $sluggable = [
        'build_from' => 'name',
        'save_to' => 'slug',
    ];

    protected $fillable = ['name', 'group'];

    public function group()
    {
        return $this->belongsToMany('App\Group')->withTimestamps();
    }

}
