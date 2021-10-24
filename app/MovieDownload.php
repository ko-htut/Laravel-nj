<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovieDownload extends Model
{
    protected $fillable = ['server', 'link', 'lang', 'embed','googledrive', 'status'];

    public function movie()
    {
        return $this->belongsTo('App\Movie');
    }

}
