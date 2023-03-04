<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $guarded = [];

    public function chats()
    {
        return $this->hasManyThrough(Chat::class, Message::class);
    }

    public function messages()
    {
        return $this->hasMany('App\Message');
    }

    public function deleteChats()
    {
        $this->chats()->where('user_id', auth()->id())->delete();
    }

    public function deleteMessages()
    {
        $this->messages()->delete();
    }

    // block a user
    public function block()
    {
        if( !$this->block ) {
            $this->block = true;
            $this->blocked_by = auth()->id();
            $this->save();
        }
    }
    
    // unblock a user
    public function unblock()
    {
        if( $this->blocked_by === auth()->id() ) {
            $this->block = false;
            $this->blocked_by = null;
            $this->save();
        }
    }
}
