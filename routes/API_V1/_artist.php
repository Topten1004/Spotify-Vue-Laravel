<?php


use Illuminate\Support\Facades\Route;

// this file contains all the artist routes


Route::group(['middleware' => 'demo'], function() {});



Route::group(['middleware' => 'demo'], function() {
    Route::apiResources(
        [
            'albums' => AlbumController::class,
            'songs' => SongController::class,
            'podcasts' => PodcastController::class,
            'episodes' => EpisodeController::class,
        ],
        [
            'except' => ['index']
        ]
    );
});








// shared routes
Route::group(['middleware' => 'demo'], function() {
    Route::post('/attach-to-album', 'AlbumController@attachSong');
    Route::post('/detach-from-album', 'AlbumController@detachSong');
});

Route::get('/prices', 'PaymentController@prices');



// exclusive routes
Route::group(['prefix' => 'artist'], function () {

    Route::get('/', 'ArtistController@account');
    Route::get('/payout-options', 'PayoutMethodController@index');
    Route::get('/notifications', 'ArtistController@notifications');
    Route::get('/analytics', 'AnalyticsController@artistIndex');
    Route::get('/plays/{duration}', 'AnalyticsController@getArtistPlays');
    Route::get('/match-songs/{keyword}', 'SongController@matchArtistSongs');
    Route::get('songs', 'SongController@artistIndex');
    Route::get('albums', 'AlbumController@artistIndex');
    Route::get('podcasts', 'PodcastController@artistIndex');
    Route::get('episodes', 'EpisodeController@artistIndex');
    Route::group(['middleware' => 'demo'], function() {
        Route::post('/contact', 'ArtistController@contact');
        Route::post('/save-personal-infos', 'ArtistController@savePersonalInfos');
        Route::post('/request-payout', 'ArtistController@requestPayout');
    });
});
