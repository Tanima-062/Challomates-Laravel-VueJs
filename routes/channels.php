<?php

use Illuminate\Support\Facades\Broadcast;

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

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Broadcast::channel('testChannel.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
Broadcast::channel('testChannel', function ($user) {
    return  true;
});

Broadcast::channel('videoShow', function () {
    return true;
});


// // Dynamic Presence Channel for Streaming
Broadcast::channel('streaming-channel.{streamId}', function ($user) {
    return ['id' => $user->id];
});

// // Signaling Offer and Answer Channels
Broadcast::channel('stream-signal-channel.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
