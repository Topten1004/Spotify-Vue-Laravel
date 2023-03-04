<?php

namespace App\Http\Controllers;

use App\Session;
use App\Events\BlockEvent;

class BlockController extends Controller
{
    /**
     * Block a user.
     * @param $session
     * @return \Illuminate\Http\Response
     */
    public function block(Session $session)
    {
        $session->block();
        broadcast(new BlockEvent($session->id, true, $session->blocked_by));
        return response(null, 201);
    }
    
    /**
     * Unblock a user.
     * @param $session
     * @return \Illuminate\Http\Response
     */
    public function unblock(Session $session)
    {
        $session->unblock();
        broadcast(new BlockEvent($session->id, false, $session->blocked_by));
        return response(null, 201);
    }
}
