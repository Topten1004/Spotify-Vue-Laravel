<?php

namespace App\Http\Controllers;

use App\Helpers\Content\ListenNotes\ListenNotes;
use App\Http\Resources\ListenNotes\Pages\PodcastResource as PagesPodcastResource;
use App\Http\Resources\ListenNotes\PodcastResource as ListenNotesPodcastResource;
use Illuminate\Http\Request;
use App\PodcastGenre;
use App\Http\Resources\PodcastGenreResource;
use App\Http\Resources\PodcastResource;
use App\Setting;
use FileManager;
use Illuminate\Database\Eloquent\Collection;

class PodcastGenreController extends Controller
{
    /**
     * Get all the podcast genres.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PodcastGenreResource::collection(PodcastGenre::all());
    }
    /**
     * Get all the genre podcasts.
     * @param  $genre_name
     * @return \Illuminate\Http\Response
     */
    public function podcasts(PodcastGenre $podcastGenre)
    {
        if ($podcastGenre) {
            $collection = new Collection();
            if( $podcastGenre->listen_notes_genre_id && Setting::get('provider_listenNotes')) {
                $listen_notes_podcasts = ListenNotes::genrePodcasts($podcastGenre->listen_notes_genre_id);
                $collection = $collection->toBase()->merge(ListenNotesPodcastResource::collection($listen_notes_podcasts));
            }
            $local_podcasts = PodcastResource::collection($podcastGenre->podcasts);
            $collection = $collection->toBase()->merge($local_podcasts);
            return  $collection; 
        } else {
            return response()->json(['message' => 'NOT_FOUND'], 404);
        }
    }
    /**
     * Display the specified resource (fetch the data for the frontend).
     *
     * @return \Illuminate\Http\Response
     */
    public function show(PodcastGenre $podcastGenre)
    {
        if ($podcastGenre) {
            return new PodcastGenreResource($podcastGenre);
        } else {
            return response()->json(['message' => 'NOT_FOUND'], 404);
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
            'name' => 'required|min:1|max:15|string',
            'slug' => 'required|unique:podcast_genres'
        ]);

        $cover = FileManager::store($request, '/covers/podcast-genres/', 'cover');

        $podcastGenre = new PodcastGenre();

        $podcastGenre->name = $request->name;
        $podcastGenre->cover = $cover;
        $podcastGenre->slug = $request->slug;

        if( $request->listen_notes_genre_id ) {
            $podcastGenre->listen_notes_genre_id = $request->listen_notes_genre_id;
        }

        $podcastGenre->save();

        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Update the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PodcastGenre  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:1|max:15|string',
            'slug' => 'required|unique:podcast_genres,slug,' . $id
        ]);

        $genre = PodcastGenre::find($id);

        if ($request->file('cover')) {
            $genre->cover = FileManager::update($request->file('cover'), $genre->cover, '/covers/podcast-genres/');
        }
        $genre->name = $request->name;
        $genre->slug = $request->slug;
        if( $request->listen_notes_genre_id ) {
            $genre->listen_notes_genre_id = $request->listen_notes_genre_id;
        }
        $genre->save();
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PodcastGenre  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $genre = PodcastGenre::find($id);
        $genre->delete();
        return response()->json(['message' => 'SUCCESS'], 200);
    }
}
