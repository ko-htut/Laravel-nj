<?php

namespace App\Http\Controllers;

use App\Embed;
use App\Genre;
use App\Http\Requests\MovieStoreRequest;
use App\Http\Requests\MovieUpdateRequest;
use App\Http\Requests\StoreImageRequest;
use App\Jobs\SendNotification;
use App\Movie;
use App\MovieGenre;
use App\MovieSubstitle;
use App\MovieVideo;
use App\Serie;
use App\Anime;
use App\Livetv;
use App\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use File;

class MovieController extends Controller
{



    // return all the movies for the admin panel
    public function web()
    
    {

        return response()->json(Movie::orderByDesc('created_at')
            ->paginate(12), 200);
    }



     // return all the movies for the admin panel
     public function search()
    
     {
 
         return response()->json(Movie::orderByDesc('created_at')
             ->paginate(12), 200);
     }
 

    // create a new movie in the database
    public function store(MovieStoreRequest $request)
    {
        $movie = new Movie();
        $movie->fill($request->movie);
        $movie->save();

        $this->onStoreMovieVideo($request,$movie);

        if ($request->movie['genres']) {
            foreach ($request->movie['genres'] as $genre) {
                $find = Genre::find($genre['id']);
                if ($find == null) {
                    $find = new Genre();
                    $find->fill($genre);
                    $find->save();
                }
                $movieGenre = new MovieGenre();
                $movieGenre->genre_id = $genre['id'];
                $movieGenre->movie_id = $movie->id;
                $movieGenre->save();
            }
        }

        if ($request->linksubs) {
            foreach ($request->linksubs as $substitle) {
                $movieSubstitle = new MovieSubstitle();
                $movieSubstitle->fill($substitle);
                $movieSubstitle->movie_id = $movie->id;
                $movieSubstitle->save();
            }
        }

        if ($request->notification) {
            $this->dispatch(new SendNotification($movie));
        }

        $data = ['status' => 200, 'message' => 'created successfully', 'body' => $movie];

        return response()->json($data, $data['status']);
    }


    public function onStoreMovieVideo($request,$movie) {

        if ($request->links) {
            foreach ($request->links as $link) {
        
                $movieVideo = new MovieVideo();
                $movieVideo->fill($link);
                $movieVideo->movie_id = $movie->id;
                $movieVideo->save();
            }
        }

    }

    // returns a especific movie
    public function show($movie)
    {


        $movie = Movie::where('id', '=', $movie)->first();


        $movie->increment('views',1);
        
        return response()->json($movie, 200);


    }

    // add a view to a movie
    public function view(Movie $movie)
    {
        if ($movie != null) {
            $movie->views++;
            $movie->save();
            $data = ['status' => 200,];
        } else {
            $data = ['status' => 400,];
        }

        return response()->json($data, $data['status']);
    }

    // update a movie in the database
    public function update(MovieUpdateRequest $request, Movie $movie)
    {
        $movie->fill($request->movie);
        $movie->save();

        $this->onUpdateMovieVideo($request,$movie);
        $this->onUpdateMovieGenre($request,$movie);
        $this->onUpdateMovieSubstitle($request,$movie);
    

        $data = ['status' => 200, 'message' => 'successfully updated', 'body' => Movie::all()];

        return response()->json($data, $data['status']);
    }



    public function onUpdateMovieVideo($request,$movie) {

        if ($request->links) {
            foreach ($request->links as $link) {
                if (!isset($link['id'])) {
                    $movieVideo = new MovieVideo();
                    $movieVideo->movie_id = $movie->id;
                    $movieVideo->fill($link);
                    $movieVideo->save();
                }
            }
        }

    }


    public function onUpdateMovieGenre($request,$movie){

        if ($request->movie['genres']) {
            foreach ($request->movie['genres'] as $genre) {
                if (!isset($genre['genre_id'])) {
                    $find = Genre::find($genre['id'] ?? 0) ?? new Genre();
                    $find->fill($genre);
                    $find->save();
                    $movieGenre = MovieGenre::where('movie_id', $movie->id)
                        ->where('genre_id', $genre['id'])->get();
                    if (count($movieGenre) < 1) {
                        $movieGenre = new MovieGenre();
                        $movieGenre->genre_id = $genre['id'];
                        $movieGenre->movie_id = $movie->id;
                        $movieGenre->save();
                    }
                }
            }
        }

    }


    public function onUpdateMovieSubstitle($request,$movie){

        if ($request->linksubs) {
            foreach ($request->linksubs as $substitle) {
                if (!isset($substitle['id'])) {
                
                    $movieVideo = new MovieSubstitle();
                    $movieVideo->movie_id = $movie->id;
                    $movieVideo->fill($substitle);
                    $movieVideo->save();
                }
            }
        }

    }

    // delete a movie in the database
    public function destroy(Movie $movie)
    {
        if ($movie != null) {
            $movie->delete();

            $data = ['status' => 200, 'message' => 'successfully removed',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, $data['status']);
    }

    // remove the genre of a movie from the database
    public function destroyGenre($genre)
    {

        if ($genre != null) {

            MovieGenre::find($genre)->delete();

            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, 200);

    }

    // save a new image in the movies folder of the storage
    public function storeImg(StoreImageRequest $request)
    {
        if ($request->hasFile('image')) {
            $filename = Storage::disk('movies')->put('', $request->image);
            $data = ['status' => 200, 'image_path' => $request->root() . '/api/movies/image/' . $filename, 'message' => 'successfully uploaded'];
        } else {
            $data = ['status' => 400, 'message' => 'could not be uploaded'];
        }

        return response()->json($data, $data['status']);
    }

    // return an image from the movies folder of the storage
    public function getImg($filename)
    {

        $image = Storage::disk('movies')->get($filename);

        $mime = Storage::disk('movies')->mimeType($filename);

        return (new Response($image, 200))->header('Content-Type', $mime);
    }

    

    // remove a video from a movie from the database
    public function videoDestroy($video)
    {
        if ($video != null) {

            MovieVideo::find($video)->delete();

            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, 200);
    }


    public function substitleDestroy($substitle)
    {
        if ($substitle != null) {

            MovieSubstitle::find($substitle)->delete();

            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, 200);
    }

    // returns 15 movies with a release date of less than 6 months
    public function latestcontent()
    {


        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->where('created_at', '>', Carbon::now()->subMonth())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->limit(7)
        ->get();

        $series = Serie::select('series.name','series.id','series.poster_path','series.vote_average')->where('created_at', '>', Carbon::now()->subMonth())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->limit(7)
        ->get();

        $array = array_merge($movies->makeHidden('videos')
        ->toArray(), $series->makeHidden('seasons')->toArray());


        return response()
            ->json(['latest' => $array], 200);

    }



    public function choosedcontent()
    {

        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->inRandomOrder()->where('active', '=', 1)->limit(10)->get();

        $series = Serie::select('series.name','series.id','series.poster_path','series.vote_average')->inRandomOrder()->where('active', '=', 1)->limit(10)->get();


        $array = array_merge($movies->makeHidden('genres')->makeHidden('genreslist')->makeHidden('videos')
        ->toArray(), $series->makeHidden('seasons','genres')
        ->makeHidden('genres')->makeHidden('genreslist')->toArray());


        return response()->json(['choosed' => $array], 200);
    }


    // return the 10 movies with the highest average votes
    public function recommendedcontent()
    {



        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->orderByDesc('vote_average')->where('active', '=', 1)->limit(10)->get();

        $series = Serie::select('series.name','series.id','series.poster_path','series.vote_average')->orderByDesc('vote_average')->where('active', '=', 1)->limit(10)->get();



        $array = array_merge($movies->makeHidden('videos')
        ->toArray(), $series->makeHidden('seasons')->toArray());


        return response()->json(['recommended' => $array], 200);
    }

    public function thisweekcontent()
    {

 


        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->where('created_at', '>', Carbon::now()->startOfWeek())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();

        $series = Serie::select('series.name','series.id','series.poster_path','series.vote_average')->where('created_at', '>', Carbon::now()->startOfWeek())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();


        $array = array_merge($movies->makeHidden('videos')
        ->toArray(), $series->makeHidden('seasons')->toArray());

        return response()
            ->json(['thisweek' => $array], 200);
    }

    public function trendingcontent()
    {


        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->where('active', '=', 1)->orderByDesc('views')->limit(10)->get();

        $series = Serie::select('series.name','series.id','series.poster_path','series.vote_average')->where('active', '=', 1)->orderByDesc('views')->limit(10)->get();


        $array = array_merge($movies->makeHidden('substitles')->makeHidden('videos')
        ->toArray(), $series->makeHidden('seasons')->toArray());



        return response()
            ->json(['trending' => $array], 200);
    }

    public function featuredcontent()

    {

        $movies = Movie::where('featured', 1)
            ->where('active', '=', 1)
            ->orderByDesc('created_at')
            ->limit(8)->get()->makeHidden([
                'overview', 'runtime', 'views', 'created_at', 'updated_at','popularity','preview','preview'
            ]);

        $series = Serie::where('featured', 1)->where('active', '=', 1)
            ->orderByDesc('created_at')
            ->limit(8)->get()->makeHidden('seasons','episodes','overview');


        $animes = Anime::where('featured', 1)->where('active', '=', 1)
            ->orderByDesc('created_at')
            ->limit(4)->get()->makeHidden('seasons','episodes','overview');
    
        $array = array_merge($movies->toArray(), $series->toArray(),$animes->toArray());

        return response()
            ->json(['featured' => $array], 200);

    }



    public function pinnedcontent()

    {


        $movies = Movie::select('movies.id','movies.title','movies.poster_path','movies.vote_average','movies.pinned')
        ->addSelect(DB::raw("'movie' as type"))->where('pinned', 1)->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();

        $series = Serie::select('series.id','series.name','series.poster_path','series.vote_average','series.pinned')
        ->addSelect(DB::raw("'serie' as type"))->where('pinned', 1)->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();


        $animes = Anime::select('animes.id','animes.name','animes.poster_path','animes.vote_average','animes.pinned')
        ->addSelect(DB::raw("'anime' as type"))->where('pinned', 1)->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();


        $array = array_merge($movies->makeHidden('videos')
        ->toArray(), $series->makeHidden('seasons')->toArray(),$animes->makeHidden('seasons')->toArray());

        return response()
            ->json(['pinned' => $array], 200);

    }



    public function topcontent()

    {

        $movies = Movie::select('movies.id','movies.title','movies.poster_path','movies.vote_average','movies.pinned')->where('active', '=', 1)
        ->orderByDesc('views')->limit(10)->get();

        $series = Serie::select('series.id','series.name','series.poster_path','series.vote_average','series.pinned')->where('active', '=', 1)
        ->orderByDesc('views')->limit(10)->get();

        $array = array_merge($movies->makeHidden('genres')->makeHidden('genreslist')->makeHidden('videos')
        ->toArray(), $series->makeHidden('seasons','genres')
        ->makeHidden('genres')->makeHidden('genreslist')->toArray());

        return response()
            ->json(['top10' => $array], 200);

    }




    public function randomcontent($statusapi)

    {

        $statusapi = Setting::first()->purchase_key;

        $movies = Movie::inRandomOrder() ->where('active', '=', 1)->limit(10)->get();
 

        return response()
            ->json(['random' => $movies], 200);

    }

    public function suggestedcontent($statusapi)

    {


        $movies = Movie::inRandomOrder() ->where('active', '=', 1)->limit(3)->get();

        $series = Serie::inRandomOrder() ->where('active', '=', 1)->limit(3)->get()->makeHidden('seasons','episodes');

        $livetv = LiveTV::inRandomOrder() ->where('active', '=', 1)->limit(3)->get();

        $animes = Anime::inRandomOrder() ->where('active', '=', 1)->limit(3)->get()->makeHidden('seasons','episodes');

        
    
        $array = array_merge($movies->toArray(), $series->toArray(),$livetv->toArray() ,$animes->toArray());

        return response()
            ->json(['suggested' => $array], 200);

    }

    // return the 10 movies with the most popularity
    public function popularcontent($statusapi)
    {


        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->where('active', '=', 1)->orderByDesc('popularity')->limit(10)->get()
        ->makeHidden('videos');

    

        return response()
            ->json(['popular' => $movies], 200);
    }

    // returns the last 10 movies added in the month
    public function recentscontent($statusapi)
    {



        $movies = Movie::where('created_at', '>', Carbon::now()->subMonth())
             ->where('active', '=', 1)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()
            ->json(['recents' => $movies], 200);
    }

    public function recentsthisweek($statusapi)
    {


        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->where('created_at', '>', Carbon::now()->startOfWeek())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();

        $series = Serie::select('series.name','series.id','series.poster_path','series.vote_average')->where('created_at', '>', Carbon::now()->startOfWeek())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')->limit(10)->get();


        $array = array_merge($movies->makeHidden('videos')
        ->toArray(), $series->makeHidden('seasons')->toArray());

       

        return response()
            ->json(['recentsthisweek' => $array], 200);
    }

    // returns 12 movies related to a movie
    public function relateds(Movie $movie)
    {
        $genre = $movie->genres[0]->genre_id;
        $movies = MovieGenre::where('genre_id', $genre)->where('movie_id', '!=', $movie->id)
            ->limit(6)
            ->get();
        $movies->load('movie');
        $relateds = [];
        foreach ($movies as $item) {
            array_push($relateds, $item['movie']);
        }

        return response()->json(['relateds' => $relateds], 200);
    }

    // return all the videos of a movie
    public function videos(Movie $movie)
    {
        return response()->json($movie->videos, 200);
    }

    // return all the videos of a movie
    public function substitles(Movie $movie)
    {
        return response()->json($movie->substitles, 200);
    }
}

