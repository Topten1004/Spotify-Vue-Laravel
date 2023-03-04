<?php

use App\Helpers\Functions;
use Illuminate\Support\Facades\Broadcast;
use App\Session;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('Chat', function ($user) {
    return ['id' => $user->id, 'name' => $user->name]; 
});

Broadcast::channel('Chat.{session_id}', function ($user, $session_id) {
    $session = Session::find($session_id);
    if ($user->id == $session->user1_id || $user->id == $session->user2_id) {
        return true;
    }
    return false;
});
