<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $timestamps = false;
    /**
     * the attributes that are mass assignable.
     * @var array
     */
    protected $fillable = ['key', 'value', 'public'];

    /**
     * the default settings for the application.
     * @var array
     */
    protected const default_settings = [
        'enablePodcasts' => 1,
        'enableBrowse' => 1,
        'aboutUs' => '',
        'allowArtistAccountRequests' => 1,
        'enableFacebookLogin' => 0,
        'enableGoogleLogin' => 0,
        'enableFriendshipSystem' => 1,
        'enableRealtime' => 0,
        'enableChat' => 0,
        'requireAuth' => 0,
        'availableUserDiskSpace' => 100,
        'availableArtistDiskSpace' => 500,
        'maxFileSize' => 5,
        'maxImageSize' => 2,
	'account_agreement' => '',
        'google_oauth_client_id' => '',
        'pusherAppId' => '',
        'pusherSecret' => '',
        'enableFriendshipSystem' => 1,
        'disableRegistration' => 0,
        'requireEmailConfirmation' => 0,
        'defaultTheme' => 'Light',
        'mailMailer' => 'smtp',
        'enableMail' => 0,
        'pusherKey' => '',
        'pusherCluster' => '',
        'mailFromAddress' => '',
        'mailFromName' => '',
        'siteTitle' => '%site_name — Play Music Anywhere & Anytime',
        'siteDescription' => '%site_name — Play Music Anywhere & Anytime',
        'homePageTitle' => 'Explore & listen | %site_name',
        'homePageDescription' => 'Explore & start listening to pure music on %site_name',
        'browsePageTitle' => 'Browse & discover music | %site_name',
        'browsePageDescription' => 'Discover & enjoy listening to pure music on %site_name',
        'podcastsPageTitle' => 'Podcasts | %site_name',
        'podcastsPageDescription' => 'Discover & enjoy listening to podcasts on %site_name',
        'artistPageDescription' => 'Enjoy hearing %artist_name on %site_name for free',
        'artistPageTitle' => '%artist_name | Play on %site_name',
        'songPageTitle' => '%song_title — %artist_name | Play On %site_name',
        'songPageDescription' => 'Play & enjoy to %song_title — Song by %artist_name on %site_name',
        'albumPageTitle' => '%artist_name — %album_title | Play on %site_name',
        'albumPageDescription' => 'Play & enjoy to %album_title — Album by %artist_name on %site_name',
        'podcastPageTitle' => '%artist_name — %podcast_title | Play on %site_name',
        'podcastPageDescription' => 'Play %podcast_title podcast — Podcast by %artist_name on %site_name',
        'playlistPageTitle' => '%playlist_title | Play on %site_name',
        'playlistPageDescription' => 'Play & enjoy to %playlist_title — Playlist by %user_name on %site_name',
        'genrePageTitle' => 'Top %genre_name music | Play on %site_name',
        'genrePageDescription' => 'Play & enjoy to %genre_name music on %site_name',
        'podcastGenrePageTitle' => 'Top %podcast-genre_name podcasts | Play on %site_name',
        'podcastGenrePageDescription' => 'Play & enjoy to %podcast-genre_name podcasts on %site_name',
        'userProfilePageTitle' => "%user_name's profile | %site_name",
        'userProfilePageDescription' => 'Check %user_name profile on %site_name',
    ];

    /**
     * Reset the settings to the default values above.
     * @var array
     */
    public static function default()
    {
        foreach (self::default_settings as $key => $value) {
            self::set($key, $value);
        }
        Setting::set('storageDisk', json_encode([
            'name' => 'Local Storage',
            'disk' => 'public'
        ]));
        return self::getAllSettings();
    }

    /**
     * CRUD functions to manage the settings.
     */

    public static function add($key, $value, $public)
    {
        return self::create(['key' => $key, 'value' => $value, 'public' => $public]);
    }
    public static function get($key)
    {
        if (self::has($key)) {
            $setting = self::getAllSettings()->where('key', $key)->first();
            return $setting->value;
        }
        return null;
    }

    public static function set($key, $value, $public = 1)
    {
        if ($key === 'enablePodcasts' && $value == '0') {
            \DB::table('navigation_items')->where('path', '/podcasts')->update(
                [
                    'visibility' => '0'
                ]
            );
        }
        if ($key === 'enableBrowse' && $value == '0') {
            \DB::table('navigation_items')->where('path', '/browse')->update(
                [
                    'visibility' => '0'
                ]
            );
        }
        if ($setting = self::getAllSettings()->where('key', $key)->first()) {
            if (isset($value)) {
                $setting->value = $value;
            } else if(self::default_settings[$key]){
                $setting->value = self::default_settings[$key];
            }
            return $setting->save();
        }
        return self::add($key, $value, $public);
    }

    public static function has($key)
    {
        return (bool) self::getAllSettings()->whereStrict('key', $key)->count();
    }

    public static function getAllSettings()
    {
        return self::all();
    }

    public static function getPublicSettings()
    {
        return self::select('key', 'value')->where('public', 1)->get();
    }
}
