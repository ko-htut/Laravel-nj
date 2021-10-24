<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovieVideo extends Model
{
    protected $fillable = ['server', 'link', 'lang', 'embed','youtubelink','supported_hosts','hls', 'status'];


    protected $casts = [
        'embed' => 'int',
        'youtubelink' => 'int',
        'supported_hosts' => 'int',
        'hls' => 'int'

    ];


    public function movie()
    {
        return $this->belongsTo('App\Movie');
    }

}
