<?php


use Illuminate\Support\Facades\Route;




// this file contains the routes accessible by logged users


Route::get('/messages', 'ChatController@messages');

Route::post('/check-friendship-status', 'FriendshipController@checkFriendshipStatus');
Route::post('/sessions/{session}/read', 'ChatController@read');
Route::post('/sessions/{session}/chats', 'ChatController@chats');

Route::group(['middleware' => 'demo'], function() {
 
    Route::post('/subscribe', 'UserController@subscribe');
    Route::post('/messages', 'ChatController@sendMessage');
    Route::post('/sessions/create', 'SessionController@create');
    
    Route::post('/sessions/{session}/clear', 'ChatController@clear');
    Route::post('/sessions/{session}/block', 'BlockController@block');
    Route::post('/sessions/{session}/unblock', 'BlockController@unblock');
    Route::post('/send/{session}', 'ChatController@send');
    
    Route::post('/add-friend', 'FriendshipController@requestFriendship');
    Route::post('/remove-friend', 'FriendshipController@removeFriend');
    
    Route::post('/make-song-public/{id}', 'SongController@makePublic');
    Route::post('/make-song-private/{id}', 'SongController@makePrivate');
    
    Route::post('/make-playlist-public/{id}', 'PlaylistController@makePublic');
    Route::post('/make-playlist-private/{id}', 'PlaylistController@makePrivate');
    
    Route::post('/attach-to-playlist', 'PlaylistController@attachSong');
    Route::post('/detach-from-playlist', 'PlaylistController@detachSong');
    
    Route::post('/mark-as-read/{id}', 'NotificationController@read');
});



Route::group(['prefix' => 'user'], function () {
    Route::post('/update-cart', 'CartController@push');
    Route::post('/remove-from-cart', 'CartController@remove');
    Route::post('/register-play', 'PlayController@userPlay');
    Route::post('/end-play/{play_id}', 'PlayController@endPLay');

    Route::get('/', 'UserController@account');

    Route::get('/likes/{type}', 'UserController@likes');
    Route::get('/follows/{type}', 'UserController@follows');

    Route::get('/playlists', 'UserController@playlists');

    Route::get('/purchases', 'UserController@purchases');
    Route::get('/recent-plays', 'UserController@recentPlays');
    Route::get('/friends', 'FriendshipController@getFriends');
    Route::get('/notifications', 'UserController@notifications');
    Route::get('songs', 'SongController@userIndex');


    Route::group(['middleware' => 'demo'], function() {
        // billing
        Route::post('/create-intent', 'PaymentController@createIntent');
        Route::post('/checkout-with-stripe', 'PaymentController@checkoutWithStripe');
        Route::post('/checkout-with-paypal', 'PaymentController@checkoutWithPaypal');

        Route::post('songs', 'SongController@store')->name('usersongs.store');
        Route::delete('songs', 'SongController@destroy')->name('usersongs.destroy');

        Route::post('playlists', 'PlaylistController@store')->name('userplaylists.store');
        Route::delete('playlists/{playlist}', 'PlaylistController@destroy')->name('userplaylists.destroy');
        Route::put('playlists/{playlist}', 'PlaylistController@update')->name('userplaylists.update');
    });


    Route::group(['middleware' => 'demo'], function() {
        Route::post('/save-account-settings', 'UserController@saveAccountSettings');
        Route::post('/cancel-subscritpion', 'UserController@cancelSubscriptionRequest');
        Route::post('/request-artist-account', 'UserController@requestArtistAccount');
        Route::post('/accept-friendship', 'FriendshipController@acceptFriendship');
        Route::post('/reject-friendship', 'FriendshipController@rejectFriendship');
        Route::post('/request-artist', 'UserController@requestArtist');
    });
});

Route::post('/logout', 'AuthController@logout');
