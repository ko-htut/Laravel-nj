<?php

namespace App\Http\Controllers;

use App\Episode;
use App\Livetv;
use App\Movie;
use App\anime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{

    const FEATURED = "featured";
    const CREATED_AT = "created_at";
    const VIEWS = "views";


    public function __construct()
    {
        $this->middleware('auth');
    }

    // navigation routes for the admin panel

    public function index()
    {
        return view('admin.index');
    }


    public function users()
    {
        return view('admin.users');
    }

    public function movies()
    {
        return view('admin.movies');
    }

    public function series()
    {
        return view('admin.series');
    }


    public function animes()
    {
        return view('admin.animes');
    }


    public function streaming()
    {
        return view('admin.streaming');
    }

    public function servers()
    {
        return view('admin.servers');
    }

    public function genres()
    {
        return view('admin.genres');
    }

    public function notifications()
    {
        return view('admin.notifications');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function account()
    {
        return view('admin.account');
    }


    public function reports()
    {
        return view('admin.reports');
    }



    public function suggestions()
    {
        return view('admin.suggestions');
    }


    public function ads()
    {
        return view('admin.ads');
    }


    public function upcomings()
    {
        return view('admin.upcomings');
    }

    public function plans()
    {
        return view('admin.plans');
    }



    public function categories()
    {
        return view('admin.categories');
    }

    public function previews()
    {
        return view('admin.previews');
    }


    public function featured()

    {


        return view('admin.featured');

    }


    // most viewed metrics

    public function topMovies()
    {
        $movies = Movie::orderBy(self::VIEWS, 'desc')->limit(5)->get();

        return response()->json($movies, 200);
    }

    public function topSeries()
    {
        $series = anime::all()->makeHidden(['seasons', 'genres'])->sortByDesc(self::VIEWS);

        if ($series->count() > 10) {
            $series = $series->take(10);
        }

        return response()->json($animes, 200);
    }

    public function topEpisodes()
    {
        $episodes = Episode::orderBy(self::VIEWS, 'desc')->limit(10)->get();

        return response()->json($episodes, 200);
    }

    public function topLivetv()
    {
        $livetv = Livetv::orderBy(self::VIEWS, 'desc')->limit(10)->get();

        return response()->json($livetv, 200);
    }

    public function topUsers()
    {
        $users = User::orderBy('id', 'desc')->limit(10)->get();

        return response()->json($users, 200);
    }





    public function moviesCount()
    {
        return response()->json([
            'status' => 'success',
            'data' => [

                'count' => [
                    'reports' => DB::table('reports')->count(),
                    'movies' => DB::table('movies')->count(),
                    'series' => DB::table('series')->count(),
                    'animes' => DB::table('animes')->count(),
                    'tvs' => DB::table('livetvs')->count(),
                    'users' => DB::table('users')->count()
                ],

            ]
        ]);
    }




}
