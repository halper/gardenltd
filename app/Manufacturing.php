<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Manufacturing extends Model
{

    protected $table = 'manufacturings';

    protected $fillable = ['name'];

    public function subcontractor()
    {
        return $this->belongsToMany('App\Subcontractor')->withPivot('site_id')->join('sites', 'site_id', '=', 'sites.id');
    }

    public function detachSubcontractor($subcontractor_id, $site_id)
    {
        DB::delete('delete from manufacturing_subcontractor where site_id = ? AND subcontractor_id = ?',[$site_id, $subcontractor_id]);
    }
    //
}
