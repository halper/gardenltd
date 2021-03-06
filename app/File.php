<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    //
    protected $table = 'files';

    protected $fillable = ['name', 'path', 'type'];

    public function fileable()
    {
        return $this->morphTo();
    }

    public function tag()
    {
        return $this->morphToMany('App\Tag', 'taggable');
    }


}
