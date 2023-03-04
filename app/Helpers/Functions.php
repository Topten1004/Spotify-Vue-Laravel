<?php

namespace App\Helpers;

use App\Http\Resources\EpisodeResource;
use App\Http\Resources\SongResource;
use App\Song;
use App\Episode;
use App\Http\Resources\RadioStationResource;
use App\RadioStation;
use App\Traits\Search;
use App\Helpers\Meta;
class Functions
{
    static public function whatIsBeingPlayed($type, $id)
    {
        if ($type === 'song') {
            $song = Search::getSong($id, false, false);
            $song_ = (object)$song;
            $id = $song_->id;
            $title = isset($song_->title) ? $song_->title: $song_->name;
            $parent_title = Meta::getAlbumTitle($song);
            $artists = Meta::getArtistName($song);
        } else if ($type === 'episode') {
            $episode = Episode::find($id);
            $id = $episode->id;
            $title = $episode->title;
            $parent_title = $episode->podcast ? $episode->podcast->title : null;
            $artists = null;
        } else if ($type === 'radio-station') {
            $radioStation = RadioStation::find($id);
            $id = $radioStation->id;
            $title = $radioStation->name;
            $parent_title = null;
            $artists = null;
        }
        return (object) [
            'id' => $id,
            'title' => $title,
            'parent_title' => $parent_title,
            'artists' => $artists,
            'type' => $type,
        ];
    }
    public static function getWhatIsHePlaying($is_playing)
    {
        $is_playing = json_decode($is_playing);
        if (!$is_playing) {
            return null;
        }
        if ($is_playing->type === 'episode') {
            $content_item  = Episode::find($is_playing->id);
        } else if ($is_playing->type === 'song') {
            $content_item = Search::getSong($is_playing->id, false);
        } else if ($is_playing->type === 'radio-station') {
            $content_item = RadioStation::find($is_playing->id);
        }
        return [
            'content_item' => $content_item,
            'title' => $is_playing->title,
            'artists' => $is_playing->artists,
            'parent_title' => $is_playing->parent_title
        ];
    }
}
