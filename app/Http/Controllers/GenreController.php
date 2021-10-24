<?php

namespace App\Http\Controllers;

use App\Genre;
use App\Http\Requests\GenreRequest;
use App\Movie;
use App\Serie;
use App\Anime;
use App\Livetv;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class GenreController extends Controller
{
    // returns all genres for the api
    public function index()
    {
        return response()->json(Genre::All(), 200);
    }

    // returns all genres for the admin panel
    public function datagenres()
    {
        return response()->json(Genre::All(), 200);
    }

    // create a new genre in the database
    public function store(GenreRequest $request)
    {
        $genre = new Genre();
        $genre->fill($request->all());
        $genre->save();

        $data = [
            'status' => 200,
            'message' => 'successfully created',
            'body' => $genre
        ];

        return response()->json($data, $data['status']);
    }

    //create or update all themoviedb genres in the database
    public function fetch(Request $request)
    {
        $genreMovies = $request->movies['genres'];
        $genreSeries = $request->series['genres'];

        foreach ($genreMovies as $genreMovie) {
            $genre = Genre::find($genreMovie['id']);
            if ($genre == null) {
                $genre = new Genre();
                $genre->id = $genreMovie['id'];
            }
            $genre->name = $genreMovie['name'];
            $genre->save();
        }

        foreach ($genreSeries as $genreSerie) {
            $genre = Genre::find($genreSerie['id']);
            if ($genre == null) {
                $genre = new Genre();
                $genre->id = $genreSerie['id'];
            }
            $genre->name = $genreSerie['name'];
            $genre->save();
        }

        $genres = Genre::all();

        $data = [
            'status' => 200,
            'message' => 'successfully updated',
            'body' => $genres
        ];

        return response()->json($data, $data['status']);
    }

    // delete a genre from the database
    public function destroy(Genre $genre)
    {
        if ($genre != null) {
            $genre->delete();
            $data = [
                'status' => 200,
                'message' => 'successfully deleted'
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be deleted'
            ];
        }

        return response()->json($data, $data['status']);
    }

    // update a genre in the database
    public function update(GenreRequest $request, Genre $genre)
    {
        if ($genre != null) {
            $genre->fill($request->all());
            $genre->save();
            $data = [
                'status' => 200,
                'message' => 'successfully updated',
                'body' => $genre
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be updated'
            ];
        }

        return response()->json($data, $data['status']);
    }

    // return all genres only with the id and name properties
    public function list()
    {

        return response()->json(['genres' => Genre::all('id', 'name')], 200);
    }




    public function showLatestAdded()
    {


        
        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->where('created_at', '>', Carbon::now()->subMonth())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->paginate(12);

        $movies->setCollection($movies->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path','videos'
        ,'substitles','vote_average','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview']));
        return $movies;
    
        return response()->json($movies, 200);
    }


    public function showByYear()
    {


        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->orderBy('release_date', 'desc')->where('active', '=', 1)
        ->paginate(12);

        $movies->setCollection($movies->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path','videos'
        ,'substitles','vote_average','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview']));
        return $movies;


      return response()->json($movies, 200);
    }


    public function showByRating()
    {


        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->orderByDesc('vote_average')->where('active', '=', 1)
        ->paginate(12);

        $movies->setCollection($movies->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path','videos'
        ,'substitles','vote_average','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview']));
        return $movies;

        return response()->json($movies, 200);

    }



    public function showByViews()
    {


        $movies = Movie::where('active', '=', 1)
        ->orderByDesc('views')
        ->paginate(12);

        $movies->setCollection($movies->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path','videos'
        ,'substitles','vote_average','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview']));
        return $movies;
    
        return response()->json($movies, 200);
    }



    public function showLatestAddedtv()
    {


        $movies = Serie::select('series.id','series.name','series.poster_path','series.vote_average')->where('created_at', '>', Carbon::now()->subMonth())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->paginate(12);

    
            $movies->setCollection($movies->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path','videos'
            ,'substitles','vote_average','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview']));
            return $movies;
    
        return response()->json($movies, 200);
    }


    public function showByYeartv()
    {


        $movies = Serie::select('series.id','series.name','series.poster_path','series.vote_average')->where('active', '=', 1)->orderBy('first_air_date', 'asc')
        ->paginate(12);

    
            $movies->setCollection($movies->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path','videos'
            ,'substitles','vote_average','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview']));
            return $movies;

   
        return response()->json($movies, 200);
    }


    public function showByRatingtv()
    {


        $movies = Serie::select('series.id','series.name','series.poster_path','series.vote_average')->where('active', '=', 1)->orderByDesc('vote_average')
        ->paginate(12);


    
            $movies->setCollection($movies->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path','videos'
            ,'substitles','vote_average','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview']));
            return $movies;

        return response()->json($movies, 200);
    }



    public function showByViewstv()
    {


        $movies = Serie::select('series.id','series.name','series.poster_path','series.vote_average')->where('active', '=', 1)->orderByDesc('views')
        ->paginate(12);

    
            $movies->setCollection($movies->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path','videos'
            ,'substitles','vote_average','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview']));
            return $movies;

    
        return response()->json($movies, 200);

    }








    public function showLatestAddedAnime()
    {


        $movies = Anime::select('animes.id','animes.name','animes.poster_path','animes.vote_average','animes.is_anime')->where('created_at', '>', Carbon::now()->subMonth())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->paginate(12);

       $movies->setCollection($movies->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path','videos'
       ,'substitles','vote_average','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview']));
       return $movies;



        return response()->json($movies, 200);
    }


    public function showByYearAnime()
    {


        $movies = Anime::select('animes.id','animes.name','animes.poster_path','animes.vote_average','animes.is_anime')->where('active', '=', 1)->orderBy('first_air_date', 'asc')
        ->paginate(12);

       $movies->setCollection($movies->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path','videos'
       ,'substitles','vote_average','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview']));
       return $movies;
   
        return response()->json($movies, 200);
    }


    public function showByRatingAnime()
    {


        $movies = Anime::select('animes.id','animes.name','animes.poster_path','animes.vote_average','animes.is_anime')->where('active', '=', 1)->orderBy('vote_average', 'asc')
        ->paginate(12);

       $movies->setCollection($movies->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path','videos'
       ,'substitles','vote_average','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview']));
       return $movies;

   
        return response()->json($movies, 200);


    }



    public function showByViewsAnime()
    {


        $movies = Anime::where('active', '=', 1)
        ->orderByDesc('views')
        ->paginate(12);

        $movies->setCollection($movies->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path']));
        return $movies;

   
        return response()->json($movies, 200);
    }



      // return all movies with all genres
      public function showMoviesAllGenres()
      {
      
        $movies = Movie::orderByDesc('created_at')->where('active', '=', 1)
        ->paginate(12);

        $movies->setCollection($movies->getCollection()->makeHidden(['genres','genreslist','overview','backdrop_path','preview_path','videos'
        ,'substitles','vote_average','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview']));
        return $movies;

        return response()->json($movies, 200);
      }


    // return all movies with all genres
    public function showSeriesAllGenres()
        {


            $movies = Serie::select('series.id','series.name','series.poster_path','series.vote_average')->orderByDesc('created_at')->where('active', '=', 1)
            ->paginate(12);
    
            $movies->setCollection($movies->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path','videos'
            ,'substitles','vote_average','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview']));
            return $movies;
    
     
        return response()->json(movies, 200);


    }

         // return all movies with all genres
    public function showAnimesAllGenres()
    {
        $animes = Anime::orderByDesc('created_at')->where('active', '=', 1)
        ->paginate(12);

        $animes->setCollection($animes->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path']));
        return $animes;

        return response()->json($animes, 200);
    }


    // return all movies of a genre
    public function showMovies(Genre $genre)
    {


        $order = 'desc';
        $movies = Movie::whereHas('genres', function ($query) use ($genre) {
            $query->where('genre_id', '=', $genre->id);
        })->where('active', '=', 1)->paginate(12);

      
        return response()->json($movies, 200);
    }


    // return all series of a genre
    public function showSeries(Genre $genre)
    {
        $series = Serie::whereHas('genres', function ($query) use ($genre) {
            $query->where('genre_id', '=', $genre->id);
        })->where('active', '=', 1)->paginate(12);
        

        return response()->json($series, 200);
    }


    public function showMoviesPlayer(Genre $genre)
    {
        $movies = Movie::whereHas('genres', function ($query) use ($genre) {
            $query->where('genre_id', '=', $genre->id);
        })->where('active', '=', 1)->paginate(6);

        return response()->json($movies, 200);
    }
    

    // return all series of a genre
    public function showSeriesPlayer(Genre $genre)
    {


        
        $series = Serie::whereHas('genres', function ($query) use ($genre) {
            $query->where('genre_id', '=', $genre->id);
        })->where('active', '=', 1)->paginate(6);


        $series->setCollection($series->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path']));
        return $series;

        return response()->json($series, 200);
    }


    public function showAnimesPlayer(Genre $genre)
    {
        $series = Anime::whereHas('genres', function ($query) use ($genre) {
            $query->where('genre_id', '=', $genre->id);
        })->where('active', '=', 1)->paginate(6);


        return response()->json($series, 200);
    }



    // return all Animes of a genre
    public function showAnimes(Genre $genre)
    {
        $animes = Anime::whereHas('genres', function ($query) use ($genre) {
            $query->where('genre_id', '=', $genre->id);
        })->where('active', '=', 1)->paginate(6);

        $animes->setCollection($animes->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path']));
        return $animes;

        return response()->json($animes, 200);
    }



    public function topteen()
    {

        

        $movies = Movie::select('movies.id','movies.title','movies.poster_path','movies.vote_average','movies.pinned','movies.views')->addSelect(DB::raw("'movie' as type"))->where('active', '=', 1)
        ->orderByDesc('views');

        $series = Serie::select('series.id','series.name','series.poster_path','series.vote_average','series.pinned','series.views')->addSelect(DB::raw("'serie' as type"))->where('active', '=', 1)
        ->orderByDesc('views');


        $query = $movies->unionAll($series);

    
        return response()->json($query->paginate(12), 200);

    }



    public function recommended()
    {

        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->addSelect(DB::raw("'movie' as type"))->orderByDesc('vote_average')->where('active', '=', 1)->paginate(12);


       $movies->setCollection($movies->getCollection()->makeHidden(['genres','genreslist','videos','substitles','overview','backdrop_path','preview_path']));
       return $movies;

        return response()->json($movies, 200);
    }



    public function choosed()
    {

        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->addSelect(DB::raw("'movie' as type"))->inRandomOrder()->where('active', '=', 1);

        $series = Serie::select('series.name','series.id','series.poster_path','series.vote_average')->addSelect(DB::raw("'serie' as type"))->inRandomOrder()->where('active', '=', 1);



        $query = $movies->unionAll($series);

        return response()->json($query->paginate(12), 200);
    }


    public function trending()
    {


        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->addSelect(DB::raw("'movie' as type"))->orderByDesc('views')->where('active', '=', 1)
        ->paginate(12);


        $movies->setCollection($movies->getCollection()->makeHidden(['genres','genreslist','videos','substitles','overview','backdrop_path','preview_path']));
        return $movies;


        return response()->json($movies, 200);
    }


    public function popularseries()
    {


        $movies = Serie::select('series.name','series.id','series.poster_path','series.vote_average')->addSelect(DB::raw("'serie' as type"))->orderByDesc('popularity')->where('active', '=', 1)->paginate(12);


        $movies->setCollection($movies->getCollection()->makeHidden(['seasons','episodes','genres','genreslist','videos','substitles','overview','backdrop_path','preview_path']));
        return $movies;

    
        return response()->json($movies, 200);
    }


    public function popularmovies()
    {


        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->addSelect(DB::raw("'movie' as type"))->orderByDesc('popularity') ->where('active', '=', 1)->orderByDesc('created_at')
        ->paginate(12);


        $movies->setCollection($movies->getCollection()->makeHidden(['genres','genreslist','videos','substitles','overview','backdrop_path','preview_path']));
        return $movies;

    

        return response()->json($movies, 200);
    }


    
    public function latestseries()
    {


        $movies = Serie::select('series.name','series.id','series.poster_path','series.vote_average')->addSelect(DB::raw("'serie' as type"))->where('created_at', '>', Carbon::now()->subMonth())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->paginate(12);

        $movies->setCollection($movies->getCollection()->makeHidden(['seasons','episodes','genres','genreslist','videos','substitles','overview','backdrop_path','preview_path']));
        return $movies;

    
        return response()->json($serie, 200);
    }



    public function new()
    {


        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->addSelect(DB::raw("'movie' as type"))->where('created_at', '>', Carbon::now()->subMonth())
        ->where('active', '=', 1)
       ->orderByDesc('created_at')
       ->paginate(12);


       $movies->setCollection($movies->getCollection()->makeHidden(['genres','genreslist','videos','substitles','overview','backdrop_path','preview_path']));
       return $movies;

       
        return response()->json($movies, 200);
    }

    public function thisweek()
    {



        $movies = Movie::select('movies.title','movies.id','movies.poster_path','movies.vote_average')->addSelect(DB::raw("'movie' as type"))->where('created_at', '>', Carbon::now()->startOfWeek())
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->paginate(12);


       $movies->setCollection($movies->getCollection()->makeHidden(['genres','genreslist','videos','substitles','overview','backdrop_path','preview_path']));
       return $movies;

    
       
    
        return response()->json($movies, 200);
    }

    public function latestanimes()
    {


        $Anime = Anime::select('animes.id','animes.name','animes.poster_path','animes.vote_average','animes.is_anime')
        ->addSelect(DB::raw("'anime' as type"))->orderByDesc('created_at')->where('active', '=', 1)->paginate(12);
    


        $Anime->setCollection($Anime->getCollection()->makeHidden(['seasons','genres','genreslist','overview','backdrop_path','preview_path','videos'
        ,'substitles','vote_average','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview']));
        return $Anime;

        return response()->json($Anime, 200);
    }




}
