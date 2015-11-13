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
    'save_to'    => 'slug',
];

    protected $table = 'subcontractors';

    protected $fillable = ['name', 'contract_date',
        'contract_start_date', 'contract_end_date', 'site_id'];


    public function site()
    {
        return $this->belongsTo('App\Site');
    }

    public function manufacturing()
    {
        return $this->belongsToMany('App\Manufacturing')->withTimestamps();
    }

}
