<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    /**
     * Create a chat for the sending user
     * @param int $session_id
     */
    public function createForSend($session_id)
    {
        return $this->chats()->create([
            'session_id' => $session_id,
            'type' => 0,
            'user_id' => Auth::user()->id
        ]);
    }

    /**
     * Create a chat for the receiving user
     * @param int $session_id
     * @param int $to_user
     */
    public function createForReceive($session_id, $to_user)
    {
        return $this->chats()->create([
            'session_id' => $session_id,
            'type' => 1,
            'user_id' => $to_user
        ]);
    }
}
