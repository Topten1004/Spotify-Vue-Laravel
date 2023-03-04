<?php


use Illuminate\Support\Facades\Route;


Route::get('/languages', 'LanguageController@indexUser');
Route::get('/messages/{locale}', 'LanguageController@appMessages');
Route::get('/track-source', 'SongController@getSource');
Route::get('/plans', 'PlanController@userIndex');
// most of these routes are protected by a middlewre (middlewares > Guest.php)
// basically if the app requires the user to authenticate him self, he have to do that otherwise
// these routes are inaccessible.

Route::get('page', 'PageController@showPageByPath');
Route::get('/navigation-items', 'NavigationItemController@index');


Route::get('/section/content/{id}', 'SectionController@content');

Route::get('/search/{keyword}', 'SearchController@index');
Route::get('/podcast-genres', 'PodcastGenreController@index');
Route::get('/genres', 'GenreController@index');

Route::post('/contact-email', 'HomeController@sendContactEmail');
Route::post('/register-play', 'PlayController@anonymousPlay');

Route::get('/highlights', 'RadioStationController@highlights');
Route::get('/get-stream-metadata-pr/{station_id}', 'RadioStationController@getMetaDataProxy');
Route::get('/get-stream-metadata-sr/{station_id}', 'RadioStationController@getMetaDataServer');

//////// charts
Route::get('/new-releases', 'SectionController@NewReleases');
Route::get('/popular-albums', 'SectionController@PopularAlbums');
Route::get('/most-liked-albums', 'SectionController@MostLikedAlbums');

Route::get('/popular-podcasts', 'SectionController@PopularPodcasts');
Route::get('/latest-podcasts', 'SectionController@LatestPodcasts');
Route::get('/best-podcasts', 'SectionController@BestPodcasts');

Route::get('/popular-songs', 'SectionController@PopularSongs');
Route::get('/most-liked-songs', 'SectionController@MostLikedSongs');

/////

Route::get('/more-from-artist', 'SongController@moreFromArtist');
Route::get('/more-from-album', 'SongController@moreFromAlbum');
Route::get('/more-from-genre/{genre_id}', 'SongController@moreFromGenre');
Route::get('/charts', 'SectionController@charts');


Route::get('/match-genres', 'GenreController@matchGenres');
Route::get('/match-artists', 'ArtistController@matchArtists');
Route::get('/match-users', 'UserController@matchUsers');
Route::get('/match-songs', 'SongController@matchSongs');
Route::get('/match-radio-stations', 'RadioStationController@matchRadioStations');
Route::get('/match-albums', 'AlbumController@matchAlbums');
Route::get('/match-podcasts', 'PodcastController@matchPodcasts');
Route::get('/match-playlists', 'PlaylistController@matchPlaylists');

Route::get('/playlist/{id}', 'PlaylistController@show');
Route::get('/genre/{id}', 'GenreController@show');
Route::get('/podcast-genre/{podcastGenre}', 'PodcastGenreController@show');
Route::get('/song/{id}', 'SongController@show');
Route::get('/podcast/{id}', 'PodcastController@show');
Route::get('/episodes/{Podcast}', 'PodcastController@episodes');
Route::get('/artist/{id}', 'ArtistController@show');
Route::get('/album/{id}', 'AlbumController@show');
Route::get('/profile/{id}', 'UserController@show');


Route::get('/pages', 'PageController@rightSidebarPages');

Route::get('/next-songs', 'SongController@nextSongs');
Route::get('/genre-songs/{genre_id}', 'GenreController@songs');
Route::get('/podcast-genre-podcasts/{podcastGenre}', 'PodcastGenreController@podcasts');




// guests will be asked to login if they want to access these routes
Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/like', 'UserController@like');
    Route::post('/dislike', 'UserController@dislike');
    Route::post('/follow', 'UserController@follow');
    Route::post('/unfollow', 'UserController@unfollow');

    Route::get('/download-song/{id}', 'SongController@download');
    Route::get('/download-episode/{id}', 'EpisodeController@download');
});
