<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/cacheClear', function() {
  $optimize = Artisan::call('optimize:clear');
  $configclear = Artisan::call('config:clear');
  return 'Routes cache cleared';
});




Route::post('/password/email', 'Api\Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('/password/reset', 'Api\Auth\ResetPasswordController@reset');
Route::get('/email/verify/{id}', 'Api\Auth\VerificationController@verify');
Route::get('/email/resend', 'Api\Auth\VerificationController@resend');


//Ads
Route::get('/ads', 'AdsController@ads');

Route::get('/animes/image/{filename}', 'AnimeController@getImg');
Route::get('/series/image/{filename}', 'SerieController@getImg');
Route::get('/livetv/image/{filename}', 'LivetvController@getImg');
Route::get('/movies/videos/{movie}', 'MovieController@videos');
Route::get('/series/episode/{episode}/{code}', 'EpisodeController@videos');
Route::get('/movies/image/{filename}', 'MovieController@getImg');
Route::get('/preview/image/{filename}', 'PreviewController@getImg');




Route::post('social_auth', 'Api\Auth\SocialAuthController@socialAuth');

Route::post('/addPlanToUser2', 'Api\Auth\LoginController@addPlanToUser2');



Route::middleware('auth:api')->group(function () {
  Route::post('/logout', 'Api\Auth\LoginController@logout');
  Route::post('/update', 'Api\Auth\LoginController@update');
  Route::post('/updatePaypal', 'Api\Auth\LoginController@updatePaypal');
  Route::post('/addPlanToUser', 'Api\Auth\LoginController@addPlanToUser');
  Route::get('/cancelSubscription', 'Api\Auth\LoginController@cancelSubscription');
  Route::get('/cancelSubscriptionPaypal', 'Api\Auth\LoginController@cancelSubscriptionPaypal');
  Route::get('/profile', 'Api\Auth\LoginController@profile');
  Route::get('/user', 'Api\Auth\LoginController@user');
  Route::put('/account/update', 'UserController@update');
  Route::get('/account/isSubscribed', 'UserController@isSubscribed');
  Route::post('/user/avatar', 'Api\Auth\LoginController@update_avatar');
  
  
  });

Route::get('/params', 'SettingController@index');
Route::get('/image/users', 'UserController@showAvatar');
Route::get('/image/logo', 'SettingController@showLogo');
Route::get('/image/minilogo', 'SettingController@showMiniLogo');
Route::get('/image/episode', 'SettingController@showEpisode');
Route::get('/image/splash', 'SettingController@showSplash');
Route::get('/image/custombanner', 'SettingController@showcustomBanner');
Route::post('/register', 'Api\Auth\RegisterController@register');
Route::post('/login', 'Api\Auth\LoginController@login');
Route::post('/disconnect', 'Api\Auth\LoginController@logout');
Route::post('/refresh', 'Api\Auth\LoginController@refresh');
Route::get('/paypal', 'Api\Auth\LoginController@paypal');


Route::middleware('auth.optional:sanctum')->group(function () {


Route::get('/servers/data/{code}', 'ServerController@servers');


  //Search
Route::get('/search/{query}/{code}', 'SearchController@index');

Route::get('/settings/{code}', 'SettingController@index');

Route::post('/report/{code}', 'ReportController@sendReport');
Route::post('/suggest/{code}', 'SuggestionsController@sendReport');



Route::get('/mediahome/featured/{code}', 'GenreController@featured');


// Animes
Route::get('/animes/show/{anime}/{code}', 'AnimeController@show');
Route::get('/animes/watch/{serie}/{code}', 'AnimeController@showbyimdb');
Route::get('/animes/recents/{code}', 'AnimeController@recents');
Route::get('/animes/relateds/{anime}/{code}', 'AnimeController@relateds');
Route::get('/animes/season/{season}/{code}', 'AnimeSeasonController@show');
Route::get('/animes/seasons/{season}/{code}', 'AnimeSeasonController@showAnimeEpisodes');



// Movies
Route::get('/media/choosedcontent/{code}', 'MovieController@choosedcontent');
Route::get('/media/latestcontent/{code}', 'MovieController@latestcontent');
Route::get('/media/recommendedcontent/{code}', 'MovieController@recommendedcontent');
Route::get('/media/popularcontent/{code}', 'MovieController@popularcontent');
Route::get('/media/recentscontent/{code}', 'MovieController@recentscontent');
Route::get('/media/thisweekcontent/{code}', 'MovieController@thisweekcontent');
Route::get('/media/recommendedcontent/{code}', 'MovieController@recommendedcontent');
Route::get('/media/trendingcontent/{code}', 'MovieController@trendingcontent');
Route::get('/media/featuredcontent/{code}', 'MovieController@featuredcontent');
Route::get('/media/pinnedcontent/{code}', 'MovieController@pinnedcontent');
Route::get('/media/suggestedcontent/{code}', 'MovieController@suggestedcontent');
Route::get('/media/randomcontent/{code}', 'MovieController@randomcontent');
Route::get('/media/relateds/{movie}/{code}', 'MovieController@relateds');
Route::get('/media/substitles/{movie}/{code}', 'MovieController@substitles');
Route::get('/media/kids/{code}', 'MovieController@kids');
Route::get('/media/view/{movie}/{code}', 'MovieController@view');
Route::get('/media/detail/{movie}/{code}', 'MovieController@show');
Route::post('/movies/sendResume/{code}', 'MoviesResumeController@sendResume');
Route::get('/movies/resume/show/{movie}/{code}', 'MoviesResumeController@show');
Route::get('/media/topcontent/{code}', 'MovieController@topcontent');
Route::get('/media/previewscontent/{code}', 'MovieController@previewscontent');


Route::get('/movies/latestadded/{code}', 'GenreController@showLatestAdded');
Route::get('/movies/byyear/{code}', 'GenreController@showByYear');
Route::get('/movies/byrating/{code}', 'GenreController@showByRating');
Route::get('/movies/byviews/{code}', 'GenreController@showByViews');


// Series

Route::get('/series/show/{serie}/{code}', 'SerieController@show');
Route::get('/series/watch/{serie}/{code}', 'SerieController@showbyimdb');
Route::get('/series/recommended/{code}', 'SerieController@recommended');
Route::get('/series/popular/{code}', 'SerieController@popular');
Route::get('/series/recentscontent/{code}', 'SerieController@recents');
Route::get('/series/kids/{code}', 'SerieController@kids');
Route::get('/series/relateds/{serie}/{code}', 'SerieController@relateds');
Route::get('/series/newEpisodescontent/{code}', 'SerieController@newEpisodes');
Route::get('/animes/newEpisodescontent/{code}', 'AnimeController@newEpisodes');
Route::get('/media/seriesEpisodesAll/{code}', 'SerieController@seriesEpisodesAll');
Route::get('/media/animesEpisodesAll/{code}', 'AnimeController@animesEpisodesAll');


Route::get('/series/latestadded/{code}', 'GenreController@showLatestAddedtv');
Route::get('/series/byyear/{code}', 'GenreController@showByYeartv');
Route::get('/series/byrating/{code}', 'GenreController@showByRatingtv');
Route::get('/series/byviews/{code}', 'GenreController@showByViewstv');

Route::get('/series/latestepisodes/{code}', 'EpisodeController@latestEpisodes');



Route::get('/animes/latestadded/{code}', 'GenreController@showLatestAddedAnime');
Route::get('/animes/byyear/{code}', 'GenreController@showByYearAnime');
Route::get('/animes/byrating/{code}', 'GenreController@showByRatingAnime');
Route::get('/animes/byviews/{code}', 'GenreController@showByViewsAnime');



// Upcoming
Route::get('/upcoming/latest/{code}', 'UpcomingController@latest');
Route::get('/upcoming/show/{upcoming}/{code}', 'UpcomingController@show');

// previews
Route::get('/previews/latest/{code}', 'PreviewController@latest');


// Seasons and Episodes
Route::get('/series/season/{season}/{code}', 'SeasonController@show');
Route::get('/series/episodeshow/{episode}/{code}', 'EpisodeController@show');
Route::get('/animes/episodeshow/{episode}/{code}', 'EpisodeController@showAnime');
Route::get('/animes/episode/{episode}/{code}', 'EpisodeController@videosAnime');
Route::get('/series/substitle/{episode}/{code}', 'EpisodeController@substitles');
Route::get('/series/view/{episode}/{code}', 'EpisodeController@view');
Route::get('/series/showEpisodeNotif/{episode}/{code}', 'SerieController@showEpisodeFromNotifcation');
Route::get('/animes/showEpisodeNotif/{episode}/{code}', 'AnimeController@showEpisodeFromNotifcation');



// Live TV


Route::get('/stream/show/{livetv}/{code}', 'LivetvController@show');
Route::get('/streaming/relateds/{movie}/{code}', 'LivetvController@relateds');
Route::get('/livetv/latest/{code}', 'LivetvController@latest');
Route::get('/livetv/featured/{code}', 'LivetvController@featured');
Route::get('/livetv/mostwatched/{code}', 'LivetvController@mostwatched');
Route::get('/livetv/show/{livetv}/{code}', 'LivetvController@show');
Route::get('/livetv/random/{livetv}/{code}', 'LivetvController@random');

//Genres
Route::get('/genres/movies/show/{genre}/{code}', 'GenreController@showMovies');
Route::get('/genres/series/show/{genre}/{code}', 'GenreController@showSeries');
Route::get('/genres/animes/show/{genre}/{code}', 'GenreController@showAnimes');
Route::get('/genres/movies/all/{code}', 'GenreController@showMoviesAllGenres');
Route::get('/genres/series/all/{code}', 'GenreController@showSeriesAllGenres');
Route::get('/genres/animes/all/{code}', 'GenreController@showAnimesAllGenres');
Route::get('/genres/list/{code}', 'GenreController@list');


Route::get('/genres/series/showPlayer/{genre}/{code}', 'GenreController@showSeriesPlayer');
Route::get('/genres/movies/showPlayer/{genre}/{code}', 'GenreController@showMoviesPlayer');
Route::get('/genres/animes/showPlayer/{genre}/{code}', 'GenreController@showAnimesPlayer');


Route::get('/genres/choosed/all/{code}', 'GenreController@choosed');
Route::get('/genres/topteen/all/{code}', 'GenreController@topteen');
Route::get('/genres/recommended/all/{code}', 'GenreController@recommended');
Route::get('/genres/trending/all/{code}', 'GenreController@trending');
Route::get('/genres/new/all/{code}', 'GenreController@new');
Route::get('/genres/popularseries/all/{code}', 'GenreController@popularseries');
Route::get('/genres/latestseries/all/{code}', 'GenreController@latestseries');
Route::get('/genres/thisweek/all/{code}', 'GenreController@thisweek');
Route::get('/genres/latestanimes/all/{code}', 'GenreController@latestanimes');
Route::get('/genres/popularmovies/all/{code}', 'GenreController@popularmovies');
Route::get('/categories/streaming/show/streaming/{code}', 'CategoryController@streamingall');



// Streaming Categories
Route::get('/categories/streaming/show/{genre}/{code}', 'CategoryController@showStreaming');
Route::get('/categories/list/{code}', 'CategoryController@list');








//Videos
Route::get('/video/{filename}', 'VideoController@show');


//Substitles
Route::get('/substitles/{filename}', 'SubstitleController@show');


//Embeds
Route::get('/embeds/show/{embed}', 'EmbedController@show');



  // Plans
  Route::get('/plans/plans/{code}', 'PlanController@plans');
  Route::post('/plans/subscribe/{code}', 'PlanController@subscribe');
  Route::get('/plans/show/{plan}/{code}', 'PlanController@show');
  Route::get('/subscriptions/all/{code}', 'PlanController@all');
  Route::get('/subscriptions/paypal/{code}', 'PlanController@paypal');






});


