<?php

namespace App;

use App\Http\Resources\GenreResource;
use App\Traits\Search;
use App\Traits\SectionContentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Section extends Model
{

    use SectionContentTrait;

    protected $guarded= [];

    public function page()
    {
        return $this->belongsTo('App\Page');
    }
    public function content()
    {
        if( !$this->endpoint )
        {
            $relations = DB::table('section_item')->where('section_id', $this->id)->get();
            if( $relations && count($relations) > 0) {
                $content = [];
                foreach ($relations as $relation) {
                    if( $relation->item_type === 'album' )
                    {
                        if( $album = Search::getAlbum($relation->item_id, false) )
                        {
                            array_push($content, $album);
                        }
                    }
                    else
                    if ( $relation->item_type === 'song' )
                    {

                        if( $song = Search::getSong($relation->item_id, false) )
                        {

                            array_push($content, $song);
                        }
                    }
                    else
                    if ( $relation->item_type === 'podcast' )
                    {

                        if( $podcast = Search::getPodcast($relation->item_id) )
                        {
                            array_push($content, $podcast);
                        }
                    }
                    else
                    if ( $relation->item_type === 'playlist' )
                    {
                        if( $playlist = Search::getPlaylist($relation->item_id, true) )
                        {
                            array_push($content, $playlist);
                        }
                    }
                    else
                    if ( $relation->item_type === 'genre' && $genre = Genre::find($relation->item_id))
                    {
                        array_push($content, new GenreResource($genre));
                    }
                    else
                    if ( $relation->item_type === 'radio-station' )
                    {
                        if( $radioStation = Search::getRadioStation($relation->item_id) )
                        {
                            array_push($content, $radioStation);
                        }
                    }
                }
                return $content;
            }
            return [];
        }
        else
        {
            if( $this->endpoint === 'new-releases' ) {
                return $this->sectionNewReleases($this->nb_labels, $this->source);
            }
             else
            if($this->endpoint === 'most-liked-songs') {
                return $this->sectionMostLikedSongs($this->nb_labels);
            }
            else
            if($this->endpoint === 'popular-songs') {
                return $this->sectionPopularSongs($this->nb_labels);
            }
            else
            if($this->endpoint === 'popular-albums') {
                return $this->sectionPopularAlbums($this->nb_labels);
            }
            else
            if($this->endpoint === 'latest-albums') {
                return $this->sectionLatestAlbums($this->nb_labels);
            }
            else
            if($this->endpoint === 'popular-podcasts') {
                return $this->sectionPopularPodcasts($this->nb_labels);
            }
            else
            if($this->endpoint === 'latest-podcasts') {
                return $this->sectionLatestPodcasts($this->nb_labels);
            }
            else
            if($this->endpoint === 'best-podcasts') {
                return $this->sectionBestPodcasts($this->nb_labels);
            }
        }
    }
}
