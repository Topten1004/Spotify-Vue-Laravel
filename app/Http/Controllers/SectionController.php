<?php

namespace App\Http\Controllers;

use App\Http\Resources\SectionResource;
use App\Traits\SectionContentTrait;

use App\Section;

use Illuminate\Http\Request;

class SectionController extends Controller
{
    use SectionContentTrait;
    /**
     * store the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'nb_labels' => 'required',
            'page_id' => 'required'
        ]);
        $section = Section::create(
            [
                'title' => $request->title,
                'endpoint' => $request->endpoint,
                'page_id' => $request->page_id,
                'nb_labels' => $request->nb_labels,
                'source' => $request->source,
                'layout' => $request->layout
            ]
        );
        $content = json_decode($request->content);
        foreach ($content as $item) {
            \DB::table('section_item')->insert([
                'section_id' => $section->id,
                'item_id' => $item->id,
                'item_type' => $item->type
            ]);
        }
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Update the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Section  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'nb_labels' => 'required',
            'page_id' => 'required'
        ]);
        $section = Section::find($id);
        $section->title =  $request->title;
        $section->endpoint =  $request->endpoint;
        $section->nb_labels =  $request->nb_labels;
        $section->page_id =  $request->page_id;
        $section->source =  $request->source;
        $section->layout =  $request->layout;
        $section->save();
        // update the content
        \DB::table('section_item')->where('section_id', $section->id)->delete();
        if( count(json_decode($request->content)) > 0 )
        {
            $content = json_decode($request->content);
            foreach ($content as $item) {
                \DB::table('section_item')->insert([
                    'section_id' => $section->id,
                    'item_id' => $item->id,
                    'item_type' => $item->type
                ]);
            }
        }
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Get the most liked songs.
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function MostLikedSongs(Request $request) 
    {
        $nb_items = $request->query('nb_items');
        $origin = $request->query('src');
        return $this->sectionMostLikedSongs($nb_items, $origin);
    }
    /**
     * Get the most liked songs.
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function MostLikedAlbums(Request $request) 
    {
        $nb_items = $request->query('nb_items');
        $origin = $request->query('src');
        return $this->sectionMostLikedAlbums($nb_items, $origin);
    }
    /**
     * Get the new released songs.
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function NewReleases(Request $request)
    {
        $nb_items = $request->query('nb_items');
        $src = $request->query('src');
        return  $this->sectionNewReleases($nb_items, $src);
    }
    /**
     * Get the most popular songs ( most played ).
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function PopularSongs(Request $request)
    {
        $nb_items = $request->query('nb_items');
        $origin = $request->query('src');
        return $this->sectionPopularSongs($nb_items, $origin);
    }
    /**
     * Get the most popular albums ( most played ).
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function PopularAlbums(Request $request)
    {
        $nb_items = $request->query('nb_items');
        $origin = $request->query('src');
        return $this->sectionPopularAlbums($nb_items, $origin);
    }
    /**
     * Get the latest released podcasts.
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function LatestPodcasts(Request $request)
    {
        $nb_items = $request->query('nb_items');
        $src = $request->query('src');
        return $this->sectionLatestPodcasts($nb_items, $src);
    }
    /**
     * Get the most popular podcasts.
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function PopularPodcasts(Request $request)
    {
        $nb_items = $request->query('nb_items');
        $src = $request->query('src');
        return $this->sectionPopularPodcasts($nb_items, $src);
    }
    /**
     * Get the most best podcasts.
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function BestPodcasts(Request $request)
    {
        $nb_items = $request->query('nb_items');
        return $this->sectionBestPodcasts($nb_items);
    }
    /**
     * Get the charts page data ( the populars, most liked, latest...etc ).
     *
     * @param int $nb_items
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function charts()
    {
        return SectionResource::collection(\App\Page::first()->sections);
    }

    public function content($id)
    {
        $section = Section::find($id);
        return  $section->content();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Section  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Section::find($id)->delete();
        return response()->json(['message' => 'SUCCESS'], 200);
    }
}
