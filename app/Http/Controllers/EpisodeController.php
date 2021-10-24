<?php

namespace App\Http\Controllers;

use App\Episode;
use App\AnimeEpisode;
use App\Serie;
use App\SerieVideo;
use App\AnimeVideo;
use App\Setting;
use Illuminate\Support\Carbon;
use App\SerieSubstitle;


class EpisodeController extends Controller
{





    public function latestEpisodes()
    {


       
        $episode = Episode::where('created_at', '>', Carbon::now()->subMonth())
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();
    

        return response()->json(['latest_episodes' => $episode], 200);


    }

    public function show($episode)
    {

        $episode = Episode::where('tmdb_id', '=', $episode)->orWhere('id', '=', $episode)->with('videos')->with('substitles')
        ->get();

        return response()->json(['episodes' => $episode], 200);


    }


    public function showAnime($episode)
    {


        $episode = AnimeEpisode::where('tmdb_id', '=', $episode)->orWhere('id', '=', $episode)->with('videos')
        ->get();


        return response()->json(['episodes' => $episode], 200);


    }

    // delete an episode from the database
    public function destroy(Episode $episode)
    {
        if ($episode != null) {
            $episode->delete();

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


    public function destroyAnime(AnimeEpisode $episode)
    {
        if ($episode != null) {
            $episode->delete();

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

    // return videos for an episode
    public function videos($episode)
    {
        

        $model = Episode::where('id', $episode)->firstOrFail();


        return response()->json(['episode_stream' => $model->videos], 200);

    }


    public function videosAnime($episode)
    {
        


        $model = AnimeEpisode::where('id', $episode)->orWhere('tmdb_id', '=', $episode)->first();

        return response()->json(['episode_stream' => $model->videos], 200);

    }


   // return substitles for an episode
   public function substitles($episode)
   {

       $model = Episode::where('id', $episode)->first();

       return response()->json(['episode_stream' => $model->substitles], 200);

   }


    // remove a video from an episode
    public function destroyVideo($id)
    {



        if ($id != null) {

            SerieVideo::find($id)->delete();

            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, 200);
       
    }


    public function destroyVideoAnime($video)
    {
        if ($video != null) {

            AnimeVideo::find($video)->delete();

            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, 200);
    }



     // remove a substitle from an episode
     public function destroySubstitles($id)
     {
         if ($id != null) {
             $video = SerieSubstitle::find($id);
             $video->delete();
 
             $data = [
                 'status' => 200,
                 'message' => 'successfully deleted ',
             ];
         } else {
             $data = [
                 'status' => 400,
                 'message' => 'could not be deleted',
             ];
         }
 
         return response()->json($data, $data['status']);
     }
 

    // add a view to an episode
    public function view(Episode $episode)
    {
        if ($episode != null) {
            $episode->views++;
            $episode->save();
            $data = [
                'status' => 200
            ];
        } else {
            $data = [
                'status' => 400
            ];
        }

        return response()->json($data, $data['status']);
    }
}
