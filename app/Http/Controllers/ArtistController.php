<?php

namespace App\Http\Controllers;

use App\Artist;
use App\ArtistFollow;
use App\Helpers\FileManager as HelpersFileManager;
use App\Traits\Search;
use FileManager;
use Illuminate\Http\Request;
use App\Http\Resources\SongResource;
use App\Http\Resources\Artist\Account as ArtistResourceAccount;
use App\Http\Resources\ArtistResource;
use App\Http\Resources\PodcastResource;
use App\Notifications\ArtistMessage;
use App\Payout;
use App\Setting;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Stevebauman\Location\Facades\Location;

class ArtistController extends Controller
{
        /**
     * Get the current artist notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function notifications()
    {
        $artist = auth()->user()->artist;
        return $artist->notifications()->orderBy('read_at')->take(5)->get();
    }
    /**
     * Get the artist account informations of the current logged user.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function account(Request $request)
    {
        if ($request->user()->artist) {
            return new ArtistResourceAccount($request->user()->artist);
        } else {
            return response()->json(['message' => 'NOT_FOUND'], 404);
        }
    }
    /**
     * Get the artist data to display the profile.
     *
     * @param  \App\Artist  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Search::getArtist($id, true);
    }

  // contact admins
  public function contact(Request $request)
  {
    $admins = User::whereHas('roles', function ($query) {
        $query->where('name', 'admin');
    })->orWhere('is_admin', 1)->get();

      $from = (object)[
          'name' => auth()->user()->artist->displayname,
          'email' => auth()->user()->email,
          'avatar' => HelpersFileManager::asset_path(auth()->user()->artist->avatar),
          'role' => __('Artist')
      ];

      Notification::send($admins, new ArtistMessage($request->title, $request->message, $request->important,  $from));

      return response()->json([], 200);
  }

    public function requestPayout(Request $request)
    {
        // Check if user has already submitted a payout request
        if( auth()->user()->artist->payouts->some( function($payout) {
            return $payout->status === 'requested';
        }))  {
            return response()->json(__('You already have a payout request. Please wait for the admins to proccess.'),500);
        }
        function price($amount){
            if( is_nan($amount) ) return 0;
            return number_format(floatval(($amount/100)), 2);
        };
        // Check if user the requested amount is lower than the artist funds
        $funds = auth()->user()->artist->funds;
        if(  $funds < $request->amount ) {
            return response()->json(__('You have only ' . price($funds) . json_decode(Setting::get('default_currency'))->symbol . ' in your account.'),500);
        }
        // Check if mimium payout amount is respected
        if(  $request->payoutOption['minimum'] > $request->amount ) {
            return response()->json(__('Please enter a value above the minimum amount!'),500);
        }

        return DB::transaction(function() use ( $request ){
            // create payout
            Payout::create([
                'artist_id' => auth()->user()->artist->id,
                'amount' => $request->amount,
                'status' => 'requested'
            ]);

            // create/update payout method for the artist
            if( !count(auth()->user()->artist->payout_method) ) {
                auth()->user()->artist->payout_method()->attach($request->payoutOption['id'], [
                    'payout_details' => $request->payoutOption['details']
                ]);
            } else {
                DB::table('payout_method_artist')->where('artist_id', auth()->user()->artist->id)->update([
                    'payout_details' =>  $request->payoutOption['details']
                ]);
            }

            return auth()->user()->artist->payouts;
            // notify admins

            // $admins = User::whereHas('roles', function ($query) {
            //     $query->where('name', 'admin');
            // })->orWhere('is_admin', 1)->get();

            // Notification::send($admins, new Message($user, new ArtistResource($artist)));
        });

        //

        return response()->json([], 200);
    }
    /**
     * Get all the artists.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return ArtistResourceAccount::collection(Artist::orderBy('created_at', 'desc')->get());
    }
    /**
     * Get all the Podcasts.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function podcasts()
    {
        return PodcastResource::collection(\Auth::user()->artist->podcasts);
    }
    /**
     * Matches the artists based on the given keyword.
     *
     * @param  $keyword
     * @return \Illuminate\Http\Response
     */
    public function matchArtists()
    {
        $engines = request()->query('engines');
        $engines = isset($engines) ? json_decode(request()->query('engines')) : ["local"];
        $keyword = request()->query('query');

        return Search::artists($keyword, $engines);
    }
    /**
     * Get the latest songs for the given artist.
     * @param $artist
     * @return \Illuminate\Http\Response
     */
    public function latest($artist)
    {
        return new SongResource($artist->song()->where('country',Location::get(Request()->ip())->countryName ?? 'Azerbaijan')->sortByDesc('created_at')->first());
    }
    /**
     * Get the popular songs for the given artist.
     * @param $artist
     * @return \Illuminate\Http\Response
     */
    public function popular($artist)
    {
        $songs = $artist->songs()
        ->where('status',0)
        ->where('country',Location::get(Request()->ip())->countryName ?? 'Azerbaijan')
        ->withCount('plays')
        ->orderBy('plays_count', 'desc')->take(5)->get();
        return $songs;
    }
    /**
     * Get the most liked songs for the given artist.
     * @param $artist
     * @return \Illuminate\Http\Response
     */
    public function most_liked($artist)
    {
        $songs = $artist->songs()
        ->where('status',0)
        ->where('country',Location::get(Request()->ip())->countryName ?? 'Azerbaijan')
        ->withCount('likes')->orderBy('likes_count', 'desc')->get();
        return $songs;
    }
    /**
     * Updates the personal informations of the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function savePersonalInfos(Request $request)
    {

        if ($id = $request->id) {
            $request->validate([
                'firstname' => 'required|min:3',
                'lastname' => 'required|min:3',
                'displayname' => 'required|min:3|unique:artists,displayname,' . $id,
                'country' => 'required',
                'email' => 'required|email|unique:artists,email,' . $id,
                'phone' => 'required',
                'address' => 'required'
            ]);

            $artist = Artist::find($id);
            if ($request->file('avatar')) {
                $artist->avatar = FileManager::update($request->file('avatar'), $artist->avatar, '/avatars/users/');
            }
            $artist->firstname = $request->firstname;
            $artist->lastname = $request->lastname;
            $artist->displayname = $request->displayname;
            $artist->phone = $request->phone;
            $artist->address = $request->address;
            $artist->email = $request->email;
            $artist->country = $request->country;

            // links
            $artist->spotify_link = $request->spotify_link;
            $artist->youtube_link = $request->youtube_link;
            $artist->soundcloud_link = $request->soundcloud_link;
            $artist->itunes_link = $request->itunes_link;
            $artist->deezer_link = $request->deezer_link;

            $artist->save();

            return response()->json(null, 202);
        } else {
            $request->validate([
                'firstname' => 'required|min:3',
                'lastname' => 'required|min:3',
                'displayname' => 'required|min:3|unique:artists',
                'country' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'address' => 'required'
            ]);
            if ($request->avatar) {
                if ($request->file('avatar')) {
                    $avatar = FileManager::store($request, '/avatars/artists/', 'avatar');
                } else {
                    $avatar = FileManager::generateFileData($request->avatar);
                }
            }
            Artist::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'displayname' => $request->displayname,
                'user_id' => auth()->user()->id,
                'country' => $request->country,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'avatar' => $avatar,
                'spotify_link' => $request->spotify_link,
                'youtube_link' => $request->youtube_link,
                'soundcloud_link' => $request->soundcloud_link,
                'itunes_link' => $request->itunes_link,
            ]);
            return response()->json(null, 201);
        }
    }
    /**
     * Checks if the current user followed the corresponding artist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function isArtistFollowed(Request $request)
    {
        $user_id = $request->user_id;
        $artist_id = $request->artist_id;
        $follow = ArtistFollow::where('user_id', $user_id)->where('artist_id', $artist_id)->first();
        if ($follow) {
            return 'true';
        } else {
            return 'false';
        }
    }

    /**
     * store the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'displayname' => 'required|min:2|unique:artists',
        ]);
        $avatar = FileManager::store($request, '/avatars/artists/', 'avatar');
        $artist = new Artist();

        $artist->firstname =  $request->firstname;
        $artist->lastname =  $request->lastname;
        $artist->displayname =  $request->displayname;
        $artist->available_disk_space = $request->available_disk_space;
        $artist->avatar = $avatar;
        $artist->country = $request->country;
        $artist->address = $request->address;
        $artist->phone = $request->phone;
        $artist->email = $request->email;
        $artist->avatar = $avatar;

        // links
        $artist->spotify_link = $request->spotify_link;
        $artist->youtube_link = $request->youtube_link;
        $artist->soundcloud_link = $request->soundcloud_link;
        $artist->itunes_link = $request->itunes_link;
        $artist->deezer_link = $request->deezer_link;

        $artist->save();

        return response()->json(null, 201);
    }

    /**
     * Update the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Artist  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'displayname' => 'required|min:2|unique:artists,displayname,' . $id,
        ]);
        $artist = Artist::find($id);
        if ($request->file('avatar')) {
            $artist->avatar = FileManager::update($request->file('avatar'), $artist->avatar, '/avatars/artists/');
        }
        $artist->firstname = $request->firstname;
        $artist->lastname = $request->lastname;
        $artist->displayname = $request->displayname;
        $artist->country = $request->country;
        $artist->address = $request->address;
        $artist->phone = $request->phone;
        $artist->email = $request->email;
        $artist->available_disk_space = $request->available_disk_space;
        // links
        $artist->spotify_link = $request->spotify_link;
        $artist->youtube_link = $request->youtube_link;
        $artist->soundcloud_link = $request->soundcloud_link;
        $artist->itunes_link = $request->itunes_link;
        $artist->deezer_link = $request->deezer_link;

        $artist->save();
        return response()->json(null, 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Artist  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $artist = Artist::find($id);
        $artist->delete();
        return response()->json(null, 202);
    }
}
