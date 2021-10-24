<?php

namespace App\Http\Controllers;

use App\Movie;
use App\Serie;
use App\Livetv;
use App\Anime;
use App\User;
use Illuminate\Http\Response;

class SearchController extends Controller
{
    // returns all the movies, animes and livetv that match the search
    public function index($query)
    {


        $movies = Movie::select('*')->where('title', 'LIKE', "%$query%")->where('active', '=', 1)->limit(10)->get();
        $series = Serie::select('*')->where('name', 'LIKE', "%$query%")->where('active', '=', 1)->limit(10)->get();
        $stream = Livetv::select('*')->where('name', 'LIKE', "%$query%")->limit(10)->get();
        $anime = Anime::select('*')->where('name', 'LIKE', "%$query%")->where('active', '=', 1)->limit(10)->get();
        $array = array_merge($movies->toArray(), $series->makeHidden('seasons','episodes')->toArray(),
        
        $stream->makeHidden('seasons','episodes')->toArray(), $anime->makeHidden('seasons','episodes')->toArray());


        return response()->json(['search' => $array], 200);
    }




    public function searchFeatured()
    {

        $query = \Request::get('q');
    	$movies = Movie::select('*')->where('title', 'LIKE', "%$query%")->where('active', '=', 1)->limit(10)->get();
        $series = Serie::select('*')->where('name', 'LIKE', "%$query%")->where('active', '=', 1)->limit(10)->get();
        $anime = Anime::select('*')->where('name', 'LIKE', "%$query%")->where('active', '=', 1)->limit(10)->get();


        $array = array_merge($movies->toArray(), $series->makeHidden('seasons','episodes')->toArray(),$anime->makeHidden('seasons','episodes')->toArray());


        return response()->json(['search' => $array], 200);


    }


    public function searchMovies()
    {
    	$query = \Request::get('q');
        $movies = Movie::select('*')->where('title', 'LIKE', "%$query%")->where('active', '=', 1)->limit(10)->get();

    	return response()->json([ 'movies' => $movies ],Response::HTTP_OK);
    }


    public function searchSeries()
    {
    	$query = \Request::get('q');
        $movies = Serie::select('*')->where('name', 'LIKE', "%$query%")->where('active', '=', 1)->limit(10)->get();

    	return response()->json([ 'series' => $movies ],Response::HTTP_OK);
    }


    
    public function searchAnimes()
    {
    	$query = \Request::get('q');
        $movies = Anime::select('*')->where('name', 'LIKE', "%$query%")->where('active', '=', 1)->limit(10)->get();

    	return response()->json([ 'animes' => $movies ],Response::HTTP_OK);
    }



    public function searchStreaming()
    {
    	$query = \Request::get('q');
        $movies = Livetv::select('*')->where('name', 'LIKE', "%$query%")->limit(10)->get();

    	return response()->json([ 'streaming' => $movies ],Response::HTTP_OK);
    }

    public function searchUsers()
    {
    	$query = \Request::get('q');
        $movies = User::select('*')->where('email', 'LIKE', "%$query%")->get();

    	return response()->json([ 'users' => $movies ],Response::HTTP_OK);
    }


}