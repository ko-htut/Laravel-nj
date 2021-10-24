<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    protected $fillable = ['tmdb_id', 'name', 'overview', 'poster_path', 'backdrop_path', 'preview_path', 'vote_average',
     'vote_count', 'popularity','featured', 'premuim','active','views', 'first_air_date', 'tv','pinned','newEpisodes','imdb_external_id','original_name'];

    protected $with = ['genres', 'seasons'];

    
    protected $appends = ['hd', 'genreslist','hasubs'];

    protected $casts = [
        'status' => 'int',
        'premuim' => 'int',
        'active' => 'int',
        'featured' => 'int',
        'pinned' => 'int',
        'newEpisodes' => 'int'
     
    ];


    public function genres()
    {
        return $this->hasMany('App\SerieGenre');
    }

    public function seasons()
    {
        return $this->hasMany('App\Season')->orderBy('season_number');
    }

    public function getHdAttribute()
    {
        $hd = 0;

        foreach ($this->seasons as $season) {
            foreach ($season->episodes as $episode) {
                foreach ($episode->videos as $video) {
                    if ($video->hd) {
                        $hd = 1;
                    }
                }
            }
        }

        return $hd;
    }


    public function gethasubsAttribute()
    {
        $hasubs = 0;

        foreach ($this->seasons as $season) {
            foreach ($season->episodes as $episode) {
                foreach ($episode->substitles as $hasubs) {
                    if ($hasubs->id) {
                        $hasubs = 1;
                    }
                }
            }
        }

        return $hasubs;
    }

    public function getGenreslistAttribute()
    {
        $genres = [];
        foreach ($this->genres as $genre) {
            array_push($genres, $genre['name']);
        }
        return $genres;
    }
}
