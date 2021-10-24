<?php

namespace App\Http\Controllers;

use App\Embed;
use App\Episode;
use App\Genre;
use App\Http\Requests\SerieStoreRequest;
use App\Http\Requests\SerieUpdateRequest;
use App\Http\Requests\StoreImageRequest;
use App\Jobs\SendNotification;
use App\Season;
use App\Serie;
use App\Anime;
use App\SerieGenre;
use App\SerieVideo;
use App\SerieSubstitle;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;



class SerieController extends Controller
{
    // returns all Series except children Series, for api.
    public function index()
    {
        $serie = Serie::whereDoesntHave('genres', function ($genre) {
            $genre->where('genre_id', '=', 10762);
        })->orderByDesc('id')->paginate(12);

        return response()->json($serie, 200);

    }



    // returns all Series for admin panel
    public function data()
    {


        return response()->json(Serie::with('seasons.episodes.videos')->orderByDesc('created_at')
        ->paginate(12), 200);


    
    }

    // returns a specific Serie
    public function show($serie)
    {

        $serie = Serie::where('id', '=', $serie)->first();

        $serie->increment('views',1);
        
        return response()->json($serie, 200);
    }


    // create a new Serie in the database
    public function store(SerieStoreRequest $request)
    {
        $serie = new Serie();
        $serie->fill($request->serie);
        $serie->save();


        $this->onSaveSerieGenre($request,$serie);
        $this->onSaveSerieSeasons($request,$serie);


        if ($request->notification) {
            $this->dispatch(new SendNotification($serie));
        }

        $data = [
            'status' => 200,
            'message' => 'successfully created',
            'body' => $serie->load('seasons.episodes.videos')
        ];

        return response()->json($data, $data['status']);
    }




    public function onSaveSerieGenre($request,$serie) {

        if ($request->serie['genres']) {
            foreach ($request->serie['genres'] as $genre) {
                $find = Genre::find($genre['id']);
                if ($find == null) {
                    $find = new Genre();
                    $find->fill($genre);
                    $find->save();
                }
                $serieGenre = new SerieGenre();
                $serieGenre->genre_id = $genre['id'];
                $serieGenre->serie_id = $serie->id;
                $serieGenre->save();
            }
        }

    }


    public function onSaveSerieSeasons($request , $serie){

        if ($request->serie['seasons']) {
            foreach ($request->serie['seasons'] as $reqSeason) {
                $season = new Season();
                $season->fill($reqSeason);
                $season->serie_id = $serie->id;
                $season->save();
               
                $this->onSaveEpisodes($request,$reqSeason,$season);

                
            }
        }

    }


    public function onSaveEpisodes($request, $reqSeason,$season) {

        if ($reqSeason['episodes']) {
            foreach ($reqSeason['episodes'] as $reqEpisode) {
                $episode = new Episode();
                $episode->fill($reqEpisode);
                $episode->season_id = $season->id;
                $episode->save();


                if (isset($reqEpisode['videos'])) {
                    foreach ($reqEpisode['videos'] as $reqVideo) {
                    
                        $video = SerieVideo::find($reqVideo['id'] ?? 0) ?? new SerieVideo();
                        $video->fill($reqVideo);
                        $video->episode_id = $episode->id;
                        $video->save();
                    }
                }
               

               $this->onSaveEpisodeSubstitle($request,$reqEpisode,$episode);
            }
        }


    }




    public function onSaveEpisodeSubstitle($request,$reqEpisode,$episode) {


         if (isset($reqEpisode['substitles'])) {
                    foreach ($reqEpisode['substitles'] as $reqVideo) {
                        $video = new SerieSubstitle();
                        $video->fill($reqVideo);
                        $video->episode_id = $episode->id;
                        $video->save();
                    }
                }
    }

    // update a Serie in the database
    public function update(SerieUpdateRequest $request, Serie $serie)
    {

        $serie->fill($request->serie);
        $serie->save();

        $this->onUpdateSerieGenre($request,$serie);
        $this->onUpdateSerieSeasons($request,$serie);


        $data = [
            'status' => 200,
            'message' => 'successfully updated',
            'body' => Serie::all()
        ];

        return response()->json($data, $data['status']);
    }




    public function onUpdateSerieGenre ($request,$serie) {

        if ($request->serie['genres']) {
            foreach ($request->serie['genres'] as $genre) {
                if (!isset($genre['genre_id'])) {
                    $find = Genre::find($genre['id']) ?? new Genre();
                    $find->fill($genre);
                    $find->save();
                    $serieGenre = SerieGenre::where('serie_id', $serie->id)->where('genre_id', $genre['id'])->get();
                    if (count($serieGenre) < 1) {
                        $serieGenre = new SerieGenre();
                        $serieGenre->genre_id = $genre['id'];
                        $serieGenre->serie_id = $serie->id;
                        $serieGenre->save();
                    }
                }
            }
        }

    }


    public function onUpdateSerieSeasons($request,$serie){


        if ($request->serie['seasons']) {
            foreach ($request->serie['seasons'] as $reqSeason) {
                $season = Season::find($reqSeason['id'] ?? 0) ?? new Season();
                $season->fill($reqSeason);
                $season->serie_id = $serie->id;
                $season->save();


                $this->onUpdateSerieEpisodes($request,$reqSeason,$season);
            }
        }


    }




    public function onUpdateSerieEpisodes ($request,$reqSeason,$season) {

        if ($reqSeason['episodes']) {
                    foreach ($reqSeason['episodes'] as $reqEpisode) {
                        $episode = Episode::find($reqEpisode['id'] ?? 0) ?? new Episode();
                        $episode->fill($reqEpisode);
                        $episode->season_id = $season->id;
                        $episode->save();
                        if (isset($reqEpisode['videos'])) {
                            foreach ($reqEpisode['videos'] as $reqVideo) {
                            
                                $video = SerieVideo::find($reqVideo['id'] ?? 0) ?? new SerieVideo();
                                $video->fill($reqVideo);
                                $video->episode_id = $episode->id;
                                $video->save();
                            }
                        }

                
                        $this->onUpdateSerieSubstitle($request,$reqEpisode,$episode);
                    }
                }

    }



    public function onUpdateSerieSubstitle ($request,$reqEpisode,$episode) {

        if (isset($reqEpisode['substitles'])) {
            foreach ($reqEpisode['substitles'] as $reqVideo) {

                $substitle = SerieSubstitle::find($reqVideo['id'] ?? 0) ?? new SerieSubstitle();
                $substitle->fill($reqVideo);
                $substitle->episode_id = $episode->id;
                $substitle->save();
            }
        

    }

}


    // delete a Serie from the database
    public function destroy(Serie $serie)
    {
        if ($serie != null) {
            $serie->delete();

            $data = [
                'status' => 200,
                'message' => 'successfully deleted',
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be deleted',
            ];
        }


        return response()->json($data, $data['status']);
    }

    // remove a genre from a Series from the database
    public function destroyGenre($genre)
    {
        if ($genre != null) {

            SerieGenre::find($genre)->delete();

            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, 200);
    }

    // save a new image in the Series folder of the storage
    public function storeImg(StoreImageRequest $request)
    {

        if ($request->hasFile('image')) {
            $filename = Storage::disk('series')->put('', $request->image);
            $data = [
                'status' => 200,
                'image_path' => $request->root() . '/api/series/image/' . $filename,
                'message' => 'image uploaded successfully'
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'there was an error uploading the image'
            ];
        }

        return response()->json($data, $data['status']);
    }

    // return an image from the Series folder of the storage
    public function getImg($filename)
    {

        $image = Storage::disk('series')->get($filename);

        $mime = Storage::disk('series')->mimeType($filename);

        return (new Response($image, 200))
            ->header('Content-Type', $mime);
    }


    // returns a specific Serie
    public function showbyimdb($serie)
    {

        $movie = Serie::where('tmdb_id', '=', $movie)->orWhere('id', '=', $movie)->first();

        $movie->increment('views',1);
        
        return response()->json($movie, 200);

    }




    // return the 10 Series with the highest average votes
    public function recommended()
    {


        $movies = Serie::select('series.id','series.name','series.poster_path','series.vote_average')
        ->orderByDesc('vote_average')->where('active', '=', 1)
        ->where('active', '=', 1)->limit(10)->get();

    return response()->json(['recommended' => 
    $movies->makeHidden(['seasons','overview','backdrop_path','preview_path','videos'
    ,'substitles','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview'])], 200);


    }

    // return the 10 movies with the most popularity
    public function popular()
    {


        $movies = Serie::select('series.id','series.name','series.poster_path','series.vote_average')->where('active', '=', 1)
        ->orderByDesc('popularity')
        ->limit(10)->get();


    return response()->json(['popularSeries' => $movies->makeHidden(['seasons','overview','backdrop_path','preview_path','videos'
    ,'substitles','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview'])], 200);


    }

    // returns the last 10 Series added in the month
    public function recents()
    {
        $movies = Serie::select('series.id','series.name','series.poster_path','series.vote_average','series.newEpisodes')->where('created_at', '>', Carbon::now()->subMonth(3))
        ->where('active', '=', 1)
        ->orderByDesc('created_at')
        ->limit(10)->get();


    return response()->json(['recents' => $movies->makeHidden(['seasons','overview','backdrop_path','preview_path','videos'
    ,'substitles','vote_count','popularity','runtime','release_date','imdb_external_id','hd','pinned','preview'])], 200);

    }


    public function relateds(Serie $serie)
    {
        $genre = $serie->genres[0]->genre_id;
        $series = SerieGenre::where('genre_id', $genre)->where('serie_id', '!=', $serie->id)
            ->limit(6)
            ->get();
        $series->load('serie')->where('active', '=', 1);
        $relateds = [];
        foreach ($series as $item) {
            array_push($relateds, $item['serie']);
        }

        return response()->json(['relateds' => $relateds], 200);
    }





    public function showEpisodeFromNotifcation($id)
    {



        $order = 'desc';
        $series = Serie::join('seasons', 'seasons.serie_id', '=', 'series.id')
        ->join('episodes', 'episodes.season_id', '=', 'seasons.id')
        ->join('serie_videos', 'serie_videos.episode_id', '=', 'episodes.id')
        ->orderBy('serie_videos.updated_at', $order)->select('serie_videos.episode_id','series.id','series.tmdb_id as serieTmdb'
        ,'series.name','episodes.still_path','episodes.season_id','episodes.name as episode_name','serie_videos.link','serie_videos.server','serie_videos.lang'
        ,'serie_videos.embed','serie_videos.youtubelink','serie_videos.hls','seasons.name as seasons_name','seasons.season_number','episodes.vote_average'
        ,'series.premuim','episodes.episode_number','series.poster_path','episodes.hasrecap','episodes.skiprecap_start_in','serie_videos.supported_hosts'
        )->addSelect(DB::raw("'serie' as type"))->where('episodes.id', '=', $id)->limit(1)->get()->makeHidden('seasons','episodes');

        $newEpisodes = [];
        foreach ($series as $item) {
            array_push($newEpisodes, $item);
        }


        return response()->json(['latest_episodes' => $newEpisodes], 200);

    }

    public function newEpisodes()
    {
        $order = 'desc';
        $series = Serie::join('seasons', 'seasons.serie_id', '=', 'series.id')
        ->join('episodes', 'episodes.season_id', '=', 'seasons.id')
        ->join('serie_videos', 'serie_videos.episode_id', '=', 'episodes.id')
        ->orderBy('serie_videos.updated_at', $order)->select('serie_videos.episode_id','series.id','series.tmdb_id as serieTmdb'
        ,'series.name','episodes.still_path','episodes.season_id','episodes.name as episode_name','serie_videos.link','serie_videos.server','serie_videos.lang'
        ,'serie_videos.embed','serie_videos.youtubelink','serie_videos.hls','seasons.name as seasons_name','seasons.season_number','episodes.vote_average'
        ,'series.premuim','episodes.episode_number','series.poster_path','episodes.hasrecap','episodes.skiprecap_start_in','serie_videos.supported_hosts'
        )->limit(10)->get()->unique('episode_id')->makeHidden('seasons','episodes');

        $newEpisodes = [];
        foreach ($series as $item) {
            array_push($newEpisodes, $item);
        }

        return response()->json(['latest_episodes' => $newEpisodes], 200);

    }





    public function seriesEpisodesAll()
    {
        $order = 'desc';
        $series = Serie::join('seasons', 'seasons.serie_id', '=', 'series.id')
        ->join('episodes', 'episodes.season_id', '=', 'seasons.id')
        ->join('serie_videos', 'serie_videos.episode_id', '=', 'episodes.id')
        ->orderBy('serie_videos.updated_at', $order)->orderBy('serie_videos.episode_id', $order)->select('serie_videos.episode_id','series.id'
        ,'series.name','episodes.still_path','episodes.season_id','episodes.name as episode_name','serie_videos.link','serie_videos.server','serie_videos.lang'
        ,'serie_videos.embed','serie_videos.youtubelink','serie_videos.hls','seasons.name as seasons_name','seasons.season_number','episodes.vote_average'
        ,'series.premuim','series.tmdb_id','episodes.episode_number',
        'series.poster_path','episodes.hasrecap','episodes.skiprecap_start_in','serie_videos.supported_hosts','series.imdb_external_id'
        )->groupBy('episode_id')->paginate(12);


        $series->setCollection($series->getCollection()->makeHidden(['seasons','episodes']));

        return $series;

        
        return response()->json($series, 200);

    }


}
