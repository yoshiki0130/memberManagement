<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prefecture extends Model
{
    public $timestamps = false;
    protected $primaryKey = "prefecture_id";

    public function user()
    {
        return $this->hasMany('App\Models\User');
    }
}
