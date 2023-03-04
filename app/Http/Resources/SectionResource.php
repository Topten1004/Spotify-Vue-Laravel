<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Traits\SectionContentTrait;

class SectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'layout' => $this->layout,
            // 'content' => $this->content(),
            'source' => $this->source,
            // 'content' => $this->getContent($this->endpoint, $this->content, $this->content_type, $this->nb_labels),
            // 'content_type' => $this->content_type,
            'endpoint' => $this->endpoint,
            'rank' => $this->rank,
            'nb_labels' => $this->nb_labels,
        ];
    }
    // /**
    //  * Fetch the data of the section content ( songs, albums, playlists...etc)
    //  * - a section is a part of page.
    //  * - The section content is stored into the database, 3 columns determine
    //  * the content of the section: 
    //  * 1- endpoint: if it is set with a value then the content of the section is NOT custom (ex: new releases).
    //  * 2- content: if the endpoint column is NULL then this must be specified and this means that
    //  * the section has custom content, it includes an array of content ( ex: [1,5,6,7] )
    //  * these values represent the IDs of the content items.
    //  * 3- content_type : works with the previous content array: determine what is the type of 
    //  * the section content ( songs, albums, playlists...etc).
    //  * @param  $endpoint
    //  * @param  $content
    //  * @param  $content_type
    //  * @param  $nb_labels
    //  * @return array
    //  */
    // private function getContent($endpoint, $content, $content_type, $nb_labels) {
    //     // checking if the section is not custom
    //     if( $endpoint ) {
    //         if( $endpoint === 'new-releases' ) {
    //             return $this->sectionNewReleases($nb_labels, 'local');
    //         } else if($endpoint === 'most-liked-songs') {
    //             return $this->sectionMostLikedSongs($nb_labels);
    //         }else if($endpoint === 'most-played-songs') {
    //             return $this->sectionMostPlayedSongs($nb_labels);
    //         }else if($endpoint === 'most-played-albums') {
    //             return $this->sectionMostPlayedAlbums($nb_labels);
    //         }else if($endpoint === 'latest-albums') {
    //             return $this->sectionLatestAlbums($nb_labels);
    //         }else if($endpoint === 'popular-podcasts') {
    //             return $this->sectionMostPlayedPodcasts($nb_labels);
    //         }else if($endpoint === 'latest-podcasts') {
    //             return $this->sectionLatestPodcasts($nb_labels);
    //         }
    //     } else {
    //         // if it is custom then fetch the items one by one
    //         return $this->fetchSectionItems($content, $content_type);
    //     }
    // }
    // /**
    // * this function will loop through the array of content ( ex: [1,5,6,7] & content_type : songs )
    // * and try to find each element of the array (id) for the corresponding model.
    //  * @param  $content
    //  * @param  $content_type
    //  * @return array
    //  */
    // private function fetchSectionItems($content, $content_type)
    // {   
    //     $assets = [];
    //     foreach( json_decode($content) as $assetId ) {
    //         if( $content_type === 'songs' ) {
    //             $asset = new SongResource(Song::find($assetId));
    //         } else if( $content_type === 'albums' ) {
    //             $asset = new AlbumResource(Album::find($assetId));
    //         } else if( $content_type === 'podcasts' ) {
    //             $asset = new PodcastResource(Podcast::find($assetId));
    //         } else if( $content_type === 'playlists' ) {
    //             $asset = new PlaylistResource(Playlist::find($assetId));
    //         } else if( $content_type === 'radio-stations' ) {
    //             $asset = new RadioStationResource(RadioStation::find($assetId));
    //         }
    //         if( isSet($asset) )
    //         array_push($assets,$asset);
    //     }
    //     return $assets;
    // }
}
