<?php

namespace App\Helpers;

use Spotify;

class Meta
{
    public static function getArtistName($item)
    {
        // convert the item into a object
        $itemObj = (object)$item;

        // item origin & type
        if ($itemObj->href) {
            $item_origin = 'spotify';
            $item_type = 'song';
        } else {
            $item_origin = 'local';
            $item_type = 'song';
        }

        if ($item_origin === 'local') {
            if ($item_type == 'song') {
                if ($itemObj->artist) {
                    return $itemObj->artist->displayname;
                } else if ($pivot = \DB::table('artist_song')->where('song_id', $itemObj->id)->first()) {
                    if ($artist = \App\Artist::find($pivot->artist_id)) {
                        return $artist->displayname;
                    } else if (\App\Setting::get('provider_spotify')) {
                        try {
                            $artist = Spotify::artist($pivot->artist_id)->get();
                            return $artist['name'];
                        } catch (Exception $e) {
                            return '';
                        }
                    } else {
                        return '';
                    }
                } else {
                    return '';
                }
            }
        } else if ( $item_origin === 'spotify' ) {
            if( count($itemObj->artists) && $itemObj->artists[0]) {
                return $itemObj->artists[0]['name'];
            }
        }   
    }

    public static function getAlbumTitle($item)
    {
      // convert the item into a object
      $itemObj = (object)$item;

      // item origin & type
      if ($itemObj->href) {
          $item_origin = 'spotify';
      } else {
          $item_origin = 'local';
      }

      if( $item_origin === ' local' ) {
          if( $itemObj->album ) {
              return $itemObj->album->title;
          } else {
              return '';
          }
      } else if ( $item_origin === 'spotify' ) {
        if( $itemObj->album ) {
            return $itemObj->album['name'];
        } else {
            return '';
        }
      }
    }
}
