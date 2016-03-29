<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];
    //
    public function file()
    {
        return $this->morphedByMany('App\File', 'taggable');
    }
}
