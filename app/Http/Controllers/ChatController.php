<?php

namespace App\Http\Controllers;

use App\Session;
use Illuminate\Http\Request;
use App\Http\Resources\chat\ChatResource;
use App\Events\PrivateChatEvent;
use Carbon\Carbon;
use App\Events\MsgReadEvent;

class ChatController extends Controller
{
    /**
     * Send a message to a user.
     * @param $session, $request
     * @return \Illuminate\Http\Response
     */
    public function send(Session $session, Request $request)
    {
        $message = $session->messages()->create(['content' => $request->content]);
        $chat = $message->createForSend($session->id);
        $message->createForReceive($session->id, $request->to_user);
        broadcast(new PrivateChatEvent($message->content, $chat));
        return response($chat->id, 200);
    }

    /**
     * Get all the chats of the user.
     * @param $session
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function chats(Session $session)
    {
        return ChatResource::collection($session->chats->where('user_id', auth()->id()));
    }

    /**
     * Read all the messages of the user chats.
     * @param $session
     */
    public function read(Session $session)
    {
        $chats = $session->chats->where('read_at', null)->where('type', 0)->where('user_id', '!=', auth()->id());
        foreach ($chats as $chat) {
            $chat->update(['read_at' => Carbon::now()]);
            broadcast(new MsgReadEvent(new ChatResource($chat), $chat->session_id));
        }
    }
}
