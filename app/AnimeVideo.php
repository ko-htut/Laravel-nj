<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnimeVideo extends Model
{
      protected $fillable = ['anime_episode_id', 'server', 'link','embed','lang','youtubelink','hls','supported_hosts'];



      protected $casts = [
        'embed' => 'int',
        'youtubelink' => 'int',
        'supported_hosts' => 'int',
        'hls' => 'int'

    ];


    public function episode()
    {
        return $this->belongsTo('App\AnimeEpisode');
    }

}
