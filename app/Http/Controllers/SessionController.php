<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Session;
use Auth;
use App\Http\Resources\chat\SessionResource;
use App\Events\SessionEvent;

class SessionController extends Controller
{
    /**
     * Create a sessions between two users ( chat ).
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $session = Session::create(['user1_id' => Auth::user()->id, 'user2_id' => $request->friend_id]);
        $modifiedSession = new SessionResource($session);
        broadcast(new SessionEvent($modifiedSession,  Auth::user()->id));
        return $modifiedSession;
    }
}
