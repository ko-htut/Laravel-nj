<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = ['name', 'image'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
