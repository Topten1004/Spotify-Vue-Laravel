<?php

namespace App\Http\Controllers;

use App\Notifications\FriendRequest;
use Illuminate\Http\Request;
use App\Http\Resources\chat\UserResource;
use App\Helpers\FileManager;
use App\Session;
use App\Friendship;

class FriendshipController extends Controller
{
    /**
     * Get all current user friends.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFriends()
    {
        $user = auth()->user();
        $friendships = Friendship::where('user_id', $user->id)->orWhere('friend_id', $user->id)->with('friend')->with('user')->get();
        $friends = $friendships->map(function ($friendship) {
            $friend = null;
            if ($friendship->user->id == auth()->user()->id) {
                $friend = $friendship->friend;
            } else {
                $friend = $friendship->user;
            }
            return $friend;
        });
        return UserResource::collection($friends);
    }
    /**
     * Sending a notification to the user: friend request.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function requestFriendship(Request $request)
    {
        $user = \App\User::find($request->user_id);
        $authUser = auth()->user();
        //checking if notification 
        $notification = \DB::table('notifications')->where('type', 'App\Notifications\FriendRequest')->where('notifiable_id', $request->user_id)->first();
        if (!$notification || json_decode($notification->data)->id !== auth()->user()->id) {
            $user->notify(new FriendRequest(['id' => $authUser->id, 'name' => $authUser->name, 'avatar' => FileManager::asset_path($authUser->avatar)]));
        }
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Accept the user firend request.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function acceptFriendship(Request $request)
    {
        $friendship = Friendship::where(function ($query) {
            global $request;
            $query->where('user_id', $request->user_id)->where('friend_id', $request->friend_id);
        })
            ->orWhere(function ($query) {
                global $request;
                $query->where('friend_id', $request->user_id)->where('user_id', $request->friend_id);
            })
            ->first();
        if (!$friendship) {
            Friendship::create([
                'user_id' => $request->user_id,
                'friend_id' => $request->friend_id
            ]);
            \App\Session::create(['user1_id' => $request->user_id, 'user2_id' => $request->friend_id]);
        }
        \DB::table('notifications')->delete($request->notification_id);
        return response()->json(null, 201);
    }
    /**
     * Reject the user firend request.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function rejectFriendship(Request $request)
    {
        \DB::table('notifications')->delete($request->notification_id);
        return response()->json(null, 201);
    }
    /**
     * Remove a friend and delete the firendship.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function removeFriend()
    {

        $friendship = Friendship::where(function ($query) {
            global $request;
            $query->where('user_id', auth()->id())->where('friend_id', $request->friend_id);
        })
            ->orWhere(function ($query) {
                global $request;
                $query->where('friend_id', auth()->id())->where('user_id', $request->friend_id);
            })
            ->first();
        $session = Session::where(function ($query) {
            global $request;
            $query->where('user1_id', auth()->id())->where('user2_id', $request->friend_id);
        })
            ->orWhere(function ($query) {
                global $request;
                $query->where('user2_id', auth()->id())->where('user1_id', $request->friend_id);
            })
            ->first();
        $friendship->delete();
        $session->delete();
        return 'success';
    }
    /**
     * Check if the current auth user is friend with a certain user.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkFriendshipStatus(Request $request)
    {
        $friend_id = $request->friend_id;
        // looking if the friendship exists
        $friendship = Friendship::where(function ($query) {
            global $request;
            $query->where('user_id', $request->user_id)->where('friend_id', $request->friend_id);
        })
            ->orWhere(function ($query) {
                global $request;
                $query->where('friend_id', $request->user_id)->where('user_id', $request->friend_id);
            })
            ->first();
        if ($friendship) {
            return 'friends';
        } else {
            $notification = \DB::table('notifications')->where('type', 'App\Notifications\FriendRequest')->where('notifiable_id', $friend_id)->first();
            if ($notification) {
                return 'requested';
            } else {
                return 'notFriends';
            }
        }
    }
}
