<?php

namespace App\Http\Controllers;

use App\Genre;
use App\Http\Resources\SongResource;
use App\Http\Resources\GenreResource;
use Illuminate\Http\Request;

use FileManager;

class GenreController extends Controller
{
    /**
     * Get all the genres.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return GenreResource::collection(Genre::all());
    }
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($genre = Genre::find($id)) {
            return new GenreResource($genre);
        } else {
            return response()->json(['message' => 'NOT_FOUND'], 404);
        }
    }
    /**
     * Get all the genre songs.
     * @param  $genre_id
     * @return \Illuminate\Http\Response
     */
    public function songs($genre_id)
    {
        return SongResource::collection(Genre::find($genre_id)->songs);
    }
    /**
     * Matches the genre based on the given keyword (search).
     *
     * @param  $keyword
     * @return \Illuminate\Http\Response
     */
    public function matchGenres()
    {
        $keyword = request()->query('query');
        return  GenreResource::collection(Genre::where('slug', 'like', '%' . \Str::slug($keyword) . '%')->get());
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
            'slug' => 'unique:genres'
        ]);

        $cover = FileManager::store($request, '/covers/genres/', 'cover');

        if (isset($request->icon)) {
            $icon = FileManager::store($request, '/icons/genres/', 'icon');
        }
        Genre::create([
            'name' => $request->name,
            'cover' => $cover,
            'slug' => $request->slug,
            'icon' => isset($icon) ? $icon : null
        ]);

        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Update the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Genre  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:1|max:15|string',
            'slug' => 'unique:genres,slug,' . $id
        ]);
        $genre = Genre::find($id);

        if (isset($request->cover) && $request->file('cover')) {
            $genre->cover = FileManager::update($request->file('cover'), $genre->cover, '/covers/genres/');
        }

        if ($request->file('icon')) {
            $genre->icon  = FileManager::update($request->file('icon'), $genre->icon, '/icons/genres/');
        } else if (!isset($request->icon)) {
            FileManager::delete($genre->icon);
            $genre->icon = null;
        }
        $genre->name = $request->name;
        $genre->slug = $request->slug;
        $genre->save();
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Genre  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $genre = Genre::find($id);
        $genre->delete();
        return response()->json(null, 202);
    }
}
