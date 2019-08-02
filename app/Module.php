<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    public function users()
    {
        return $this->belongsToMany('App\User', 'user_completed_modules','module_id');
    }
}
