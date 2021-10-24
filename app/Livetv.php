<?php

namespace App;




use Illuminate\Database\Eloquent\Model;

class Livetv extends Model


{
    protected $fillable = ['name', 'overview', 'poster_path', 'backdrop_path', 'link','vip','active','featured','embed','hls'];



    protected $appends = ['genreslist'];


    protected $casts = [
        'featured' => 'int',
        'embed' => 'int',
        'status' => 'int',
        'live' => 'int',
        'active' => 'int',
        'vip' => 'int',
        'hls' => 'int',
        'views' => 'int'
    ];



    public function genres()
    {
        return $this->hasMany('App\LivetvGenre');
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
