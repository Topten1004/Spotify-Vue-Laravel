<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\PodcastResource;
use App\Podcast;
use FileManager;
use App\Traits\Search;

class PodcastController extends Controller
{
    /**
     * Display the specified resource (fetch the needed data for the frontend).
     *
     * @param  \App\Podcast  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Search::getPodcast($id, true);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PodcastResource::collection(\App\Podcast::orderBy('created_at', 'desc')->get());
    }
    /**
     * Display a listing of the resource for the current logged artist.
     *
     * @return \Illuminate\Http\Response
     */
    public function artistIndex()
    {
        return PodcastResource::collection(\Auth::user()->artist->podcasts()->orderBy('created_at', 'desc')->get());
    }
    /**
     * Matches the podcasts based on the given keyword.
     *
     * @param  $keyword
     * @return \Illuminate\Http\Response
     */
    public function matchPodcasts()
    {
        $engines = request()->query('engines');
        $engines = isset($engines) ? json_decode(request()->query('engines')) : ["local"];
        $keyword = request()->query('query');

        return Search::podcasts($keyword, $engines, true);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Podcast  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $podcast = Podcast::find($id);
        $podcast->delete();
        return response()->json(['message' => 'SUCCESS'], 200);
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
            'title' => 'required|max:255'
        ]);
        $cover = FileManager::store($request, '/covers/podcasts/', 'cover');
        $podcast = new Podcast();

        $podcast->title = $request->title;
        $podcast->description = isset($request->description) ? $request->description : '';
        $podcast->artist_id = $request->artist_id ? $request->artist_id : null;
        $podcast->cover = $cover;

        // links
        $podcast->spotify_link = $request->spotify_link;
        $podcast->youtube_link = $request->youtube_link;
        $podcast->soundcloud_link = $request->soundcloud_link;
        $podcast->itunes_link = $request->itunes_link;
        $podcast->deezer_link = $request->deezer_link;


        $podcast->save();
        
        if (json_decode($request->genres)) {
            foreach (json_decode($request->genres) as $genre) {
                $podcast->genres()->attach($genre->id);
            }
        }
        return response()->json(null, 202);
    }

    // /**
    //  * Matches the podcasts based on the given keyword.
    //  *
    //  * @param  $keyword
    //  * @return \Illuminate\Database\Eloquent\Collection
    //  */
    // public function matchPodcast()
    // {
    //     $engine = request()->query('engine');
    //     $keyword = request()->query('query');

    //     return Search::podcasts($keyword, $engine);
    // }
    /**
     * Update the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Podcast  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255'
        ]);
        
        $podcast = Podcast::find($id);
        if ($request->file('cover')) {
            $podcast->cover = FileManager::update($request->file('cover'), $podcast->cover, '/covers/podcasts/');
        }
        $podcast->title = $request->title;
        $podcast->artist_id = $request->artist_id ? $request->artist_id : null;
        $podcast->description = isset($request->description) ? $request->description : '';
        // links
        $podcast->spotify_link = $request->spotify_link;
        $podcast->youtube_link = $request->youtube_link;
        $podcast->soundcloud_link = $request->soundcloud_link;
        $podcast->itunes_link = $request->itunes_link;
        $podcast->deezer_link = $request->deezer_link;
        $podcast->save();
        // reset genres
        \DB::table('genre_podcast')->where('podcast_id', $podcast->id)->delete();
        if (json_decode($request->genres)) {
            foreach (json_decode($request->genres) as $genre) {
                $podcast->genres()->attach($genre->id);
            }
        }
        return response()->json(null, 202);
    }
}
