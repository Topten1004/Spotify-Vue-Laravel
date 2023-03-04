<?php

namespace App\Http\Controllers;

use App\Artist;
use App\Events\HearingEvent;
use App\Helpers\Functions;
use App\Play;
use App\Royalty;
use App\Setting;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class PlayController extends Controller
{
        /**
     * When a user play a song his status is gonna be updated ( is_playing ).
     * When it's ended or the user quit the page this function resets the recent status to 'null'.
     * @return \Illuminate\Http\Response
     */
    public function endPlay($play_id)
    {
        if (auth()->user()) {
            auth()->user()->is_playing = null;
            auth()->user()->save();
            if (Setting::get('enableRealtime')) {
                try {
                    broadcast(new HearingEvent(auth()->user()->id, null));
                } catch (Exception $e) {}
            }
            $play = Play::find($play_id);
            if( $play &&  ($play->content_type === 'song' || $play->content_type === 'episode') ) {
                if( Carbon::now() >= $play->end_play_expectation ) { 
                    if( Setting::get('saas') && Setting::get('enable_artist_account') && Setting::get('royalties')) {
                        //increase the artist funds if artist is the seller of the product
                            if( $artist_id = $play->artist_id ) {
                                $play_royalty = Royalty::create([
                                    'artist_id' => $artist_id,
                                    'price' => Setting::get('artist_royalty')
                                ]);
                                $artist = Artist::find( $artist_id );
                                $artist->funds += $play_royalty->price / 100;
                                $artist->save();
                            }
                        }
                } else {
                    $play->delete();
                }
            }
        }
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * When a user play a song his status is gonna be updated ( is_playing ),
     * this function updates the recent status and increase the played 
     * song count.
     *
     * @return \Illuminate\Http\Response
     */
    public function userPlay(Request $request)
    {
        $user_id =  auth()->user()->id;
        if (Setting::get('enableRealtime') && ($request->type === 'episode' || $request->type == 'song' || $request->type == 'radio-station')) {
            $played_content = Functions::whatIsBeingPlayed($request->type, $request->id);
            if (!auth()->user()->hide_activity) {
                auth()->user()->is_playing = json_encode($played_content);
                auth()->user()->save();
                try {
                    broadcast(new HearingEvent(auth()->user()->id, $played_content));
                } catch (Exception $e) {} 
            }
        }
        $play = Play::create([
            'user_id' => $user_id,
            'content_type' => $request->type,
            'artist_id' => $request->artist_id,
            'content_id' => $request->id,
            'content_source' => isset($request->origin)? $request->origin : 'local' ,
            'end_play_expectation' => $request->duration ? Carbon::now()->addSeconds($request->duration - 10): Carbon::now()->addHours(1),
        ]);
        return $play->id;
    }
    /**
     * Increase the played song count.
     *
     * @return \Illuminate\Http\Response
     */
    public function anonymousPlay(Request $request)
    {
        Play::create([
            'content_id' => $request->id,
            'content_type' => $request->type
        ]);

        if( Setting::get('enable_artist_account') ) {
            //increase the artist funds if artist is the seller of the product
            if( $artist_id = $request->artist_id ) {
                $play_royalty = Royalty::create([
                    'artist_id' => $request->artist_id,
                    'price' => Setting::get('artist_royalty')
                ]);
                $artist = Artist::find( $artist_id );
                $artist->funds += $play_royalty->price / 100;
                $artist->save();
            }
        }
        return response()->json(['message' => 'SUCCESS'], 200);
    }
}
