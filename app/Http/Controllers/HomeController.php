<?php

namespace App\Http\Controllers;

use App\Album;
use App\Genre;
use App\Helpers\FileManager;
use App\Setting;
use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Session;
// use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Language;
use App\Mail\contactMail;
use App\PodcastGenre;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use stdClass;
use App\Helpers\Meta as MetaHelper;

use App\Traits\Search;
use Butschster\Head\Facades\Meta;
use Butschster\Head\Packages\Entities\OpenGraphPackage;
use Butschster\Head\Packages\Entities\TwitterCardPackage;
use Illuminate\Support\Facades\Artisan;

class HomeController extends Controller
{
    const CRAWLERS = [
        'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.96 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1 (compatible; AdsBot-Google-Mobile; +http://www.google.com/mobile/adsbot.html)',
        'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Safari/537.36',
        'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)',
        'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/534+ (KHTML, like Gecko) BingPreview/1.0b',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)',
        'Googlebot-Image/1.0',
        'Mediapartners-Google',
        'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)',
        'facebookexternalhit/1.1',
        'Twitterbot/1.0',
        'TelegramBot (like TwitterBot)',
    ];

    public function index()
    {
        if ($this->isACrawler()) {
            $this->applyMetaTags();

            return view('layouts.crawler');
        }

        return view('layouts.index');
    }

    public function isACrawler()
    {
        if (in_array(request()->userAgent(), self::CRAWLERS)) {
            return true;
        }

        return false;
    }

    private function defaultMetaTags()
    {
        Meta::addMeta('theme-color', [
            'content' => '#fff'
        ]);

        $twitter = new TwitterCardPackage('Twitter');

        $twitter->setType('summary');

        Meta::registerPackage($twitter);
    }
    private function applyMetaTags()
    {
        preg_match("/album\/[a-zA-Z0-9]+/i", request()->path(), $album);
        preg_match("/song\/[a-zA-Z0-9]+/i", request()->path(), $song);
        preg_match("/artist\/[a-zA-Z0-9]+/i", request()->path(), $artist);
        preg_match("/podcast\/[a-zA-Z0-9]+/i", request()->path(), $podcast);
        preg_match("/podcasts\/[a-zA-Z]+/i", request()->path(), $podcastGenre);
        preg_match("/genre\/[a-zA-Z0-9]+/i", request()->path(), $genre);


        if (isset($album) && count($album)) {
            preg_match("/\/[a-zA-Z0-9]+/i", $album[0], $ids);

            $id = preg_replace("/\//", "", $ids[0]);

            $album = Search::getAlbum($id, false, false);

            if ($album) {
                $album = (object)$album;

                if(isset(((object)$album)->artist)) {
                    $artist_name = $album->artist->displayname;
                } else if( $album->uri && $album->artists && count($album->artists) ) {
                    $artist_name = ((object)$album)->artists[0]['name'];
                } else if( $album->artists() && count($album->artists())  ) {
                    if( $album->artists()[0]['name'] ) {
                        $artist_name = $album->artists()[0]['name'];
                    } else {
                        $artist_name = $album->artists()[0]['displayname'];
                    }
                }
                // $artist_name = isset($album->artist['name']) ? $album->artist['name'] : ($album->artist ? $album->artist->displayname: '');

                $album_title = isset($album->name) ? $album->name: $album->title;

                if( isset($album->cover) ){
                    $album_cover = FileManager::asset_path($album->cover);
                } else if ( isset($album->images)) {
                    $album_cover =  $album->images[1]['url'];
                } else {
                    $album_cover = '';
                }

                $title = $this->replaceVariables('%album_title', $album_title, $artist_name, Setting::get('albumPageTitle'));
                $description = $this->replaceVariables('%album_title', $album_title, $artist_name, Setting::get('albumPageDescription'));

                Meta::prependTitle($title)
                    ->addMeta('description', ['content' => $description]);

                $og = new OpenGraphPackage('album');


                $og->setType('music.album')
                    ->setSiteName(config('app.name'))
                    ->setTitle($title)
                    ->setUrl(request()->fullUrl())
                    ->setDescription($description);

                $og->addImage(FileManager::asset_path($album_cover), [
                    'width' => 300,
                    'height' => 300
                ]);

                $twitter = new TwitterCardPackage('Twitter');

                $twitter->setType('summary');

                Meta::registerPackage($og);
                Meta::registerPackage($twitter);
            }
        } else if (isset($podcast) && count($podcast)) {
            preg_match("/podcast\/[a-zA-Z0-9]+/i", $podcast[0], $ids);
       
            $id = preg_replace("/podcast\//", "", $ids[0]);
            $podcast = Search::getPodcast($id, false, false);
            if ($podcast) {

                if( isset($podcast->publisher) ) {
                    $artist_name = $podcast->publisher;
                } else if( $podcast->artist()) {
                    $artist = (object) $podcast->artist()->toArray(request());
                    $artist_name = $artist->displayname ;
                } else {
                    $artist_name = __('Unknown podcaster');
                }


                $title = $this->replaceVariables('%podcast_title', $podcast->title, $artist_name, Setting::get('podcastPageTitle'));
                $description = $this->replaceVariables('%podcast_title', $podcast->title, $artist_name, Setting::get('podcastPageDescription'));

                if( isset($podcast->image) ) {
                    $cover = $podcast->image;
                } else {
                    $cover = FileManager::asset_path($podcast->cover); 
                }
                Meta::prependTitle($title)
                    ->addMeta('description', ['content' => $description]);

                $og = new OpenGraphPackage('podcast');


                $og->setType('podcast')
                    ->setSiteName(config('app.name'))
                    ->setTitle($title)
                    ->setUrl(request()->fullUrl())
                    ->setDescription($description);

                $og->addImage(FileManager::asset_path($cover), [
                    'width' => 300,
                    'height' => 300
                ]);

                $twitter = new TwitterCardPackage('Twitter');

                $twitter->setType('summary');

                Meta::registerPackage($og);
                Meta::registerPackage($twitter);
            }
        } else if (isset($song) && count($song)) {

            preg_match("/\/[a-zA-Z0-9]+/i", $song[0], $ids);

            $id = preg_replace("/\//", "", $ids[0]);

            $song = Search::getSong($id, false, false);
            if ($song) {
                $artist_name = MetaHelper::getArtistName($song);
                $song_title = isset(((object)$song)->title) ? ((object)$song)->title : ((object)$song)->name;
                $song_cover = isset(((object)$song)->cover) ? ((object)$song)->cover: ((object)$song)->album['images']['1']['url'];
                $title = $this->replaceVariables('%song_title', $song_title, $artist_name, Setting::get('songPageTitle'));
                $description = $this->replaceVariables('%song_title', $song_title, $artist_name, Setting::get('songPageDescription'));

                Meta::prependTitle($title)
                    ->addMeta('description', ['content' => $description]);

                $og = new OpenGraphPackage('song');


                $og->setType('music.song')
                    ->setSiteName(config('app.name'))
                    ->setTitle($title)
                    ->setUrl(request()->fullUrl())
                    ->setDescription($description);

                $og->addImage(FileManager::asset_path($song_cover), [
                    'width' => 300,
                    'height' => 300
                ]);

                $twitter = new TwitterCardPackage('Twitter');

                $twitter->setType('summary');

                Meta::registerPackage($og);
                Meta::registerPackage($twitter);
            }
        } else if (isset($artist) && count($artist)) {

            preg_match("/\/[a-zA-Z0-9]+/i", $artist[0], $ids);

            $id = preg_replace("/\//", "", $ids[0]);

            $artist = Search::getArtist($id, false);

            if ($artist) {

                $artist = (object) $artist->toArray(request());
                $artist_name = isset($artist->name) ? $artist->name : $artist->displayname;

                $title = $this->replaceVariables('%artist_name',  $artist_name, $artist_name, Setting::get('artistPageTitle'));
                $description = $this->replaceVariables('%artist_name',  $artist_name, $artist_name, Setting::get('artistPageDescription'));

                Meta::prependTitle($title)
                    ->addMeta('description', ['content' => $description]);

                $og = new OpenGraphPackage('artist');


                $og->setType('music.artist')
                    ->setSiteName(config('app.name'))
                    ->setTitle($title)
                    ->setUrl(request()->fullUrl())
                    ->setDescription($description);

                $og->addImage(FileManager::asset_path($artist->avatar), [
                    'width' => 300,
                    'height' => 300
                ]);

                $twitter = new TwitterCardPackage('Twitter');

                $twitter->setType('summary');

                Meta::registerPackage($og);
                Meta::registerPackage($twitter);
            }
        } else if (isset($podcastGenre) && count($podcastGenre)) {

            preg_match("/\/[a-zA-Z0-9]+/i", $podcastGenre[0], $ids);

            $id = preg_replace("/\//", "", $ids[0]);

            $podcastGenre = PodcastGenre::where('name', 'like', '%' . \Str::slug($id) . '%')->first();;

            if ($podcastGenre) {

                $podcastGenre_name = $podcastGenre->name;

                $title = $this->replaceVariables('%podcast-genre_name',  $podcastGenre_name, $podcastGenre_name, Setting::get('podcastGenrePageTitle'));
                $description = $this->replaceVariables('%podcast-genre_name',  $podcastGenre_name, $podcastGenre_name, Setting::get('podcastGenrePageDescription'));

                Meta::prependTitle($title)
                    ->addMeta('description', ['content' => $description]);

                $og = new OpenGraphPackage('podcast-genre');


                $og->setType('podcast.genre')
                    ->setSiteName(config('app.name'))
                    ->setTitle($title)
                    ->setUrl(request()->fullUrl())
                    ->setDescription($description);

                $og->addImage(FileManager::asset_path($podcastGenre->cover), [
                    'width' => 300,
                    'height' => 300
                ]);

                $twitter = new TwitterCardPackage('Twitter');

                $twitter->setType('summary');

                Meta::registerPackage($og);
                Meta::registerPackage($twitter);
            }
        } else if (isset($genre) && count($genre)) {

            preg_match("/\/[a-zA-Z0-9]+/i", $genre[0], $ids);

            $id = preg_replace("/\//", "", $ids[0]);

            $genre = Genre::find($id);

            if ($genre) {

                $genre_name = $genre->name;

                $title = $this->replaceVariables('%genre_name',  $genre_name, $genre_name, Setting::get('genrePageTitle'));
                $description = $this->replaceVariables('%genre_name',  $genre_name, $genre_name, Setting::get('genrePageDescription'));

                Meta::prependTitle($title)
                    ->addMeta('description', ['content' => $description]);

                $og = new OpenGraphPackage('genre');


                $og->setType('music.genre')
                    ->setSiteName(config('app.name'))
                    ->setTitle($title)
                    ->setUrl(request()->fullUrl())
                    ->setDescription($description);

                $og->addImage(FileManager::asset_path($genre->cover), [
                    'width' => 300,
                    'height' => 300
                ]);

                $twitter = new TwitterCardPackage('Twitter');

                $twitter->setType('summary');

                Meta::registerPackage($og);
                Meta::registerPackage($twitter);
            }
        } else {
            $title = Cache::remember('title', 7200, function () {
                return str_replace('%site_name', Setting::get('appName'), Setting::get('siteTitle'));
            });

            $description = Cache::remember('description', 7200, function () {
                return str_replace('%site_name', Setting::get('appName'), Setting::get('siteDescription'));
            });

            $site = new stdClass();

            $site->title = $title;
            $site->name = config('app.name');
            $site->url = config('app.url');
            $site->description = $description;

            Meta::prependTitle($site->title)
                ->addMeta('description', ['content' => $site->description]);

            $og = new OpenGraphPackage('site');

            $og->setType('website')
                ->setSiteName($site->name)
                ->setTitle($site->title)
                ->setUrl(request()->fullUrl())
                ->setDescription($site->description);


                $og->addImage(asset('/images/website_image_300x300.png'), [
                'width' => 300,
                'height' => 300
            ]);
            Meta::registerPackage($og);
        }
    }
    /**
     * Update the location of the user which is stored on the session.
     * @return \Illuminate\Http\Response
     */
    // static function updateLocation()
    // {
    //     $position = \Location::get();
    //     if ($position) {
    //         if (\DB::table('visiting_countries')->where('name', $position->countryName)->first()) {
    //             \DB::table('visiting_countries')->where('name', $position->countryName)->increment('visitors');
    //         } else {
    //             \DB::table('visiting_countries')->insert([
    //                 'name' => $position->countryName ? $position->countryName : '',
    //                 'visitors' => 1
    //             ]);
    //         }
    //         if (\DB::table('visiting_browsers')->where('name', Browser::browserFamily())->first()) {
    //             \DB::table('visiting_browsers')->where('name', Browser::browserFamily())->increment('visitors');
    //         } else {
    //             \DB::table('visiting_browsers')->insert([
    //                 'name' => Browser::browserFamily(),
    //                 'visitors' => 1
    //             ]);
    //         }
    //         Session::put('current_ip', $position->ip);
    //         Session::put('location_updated_at', Carbon::now()->addMinutes(15));
    //     }
    // }
    /**
     * Checks if session time has exprired ( 15 mins ).
     * @return \Illuminate\Http\Response
     */
    // static function checkSessionTime()
    // {
    //     if (Session::get('location_updated_at') <= Carbon::now()) {
    //         self::updateLocation();
    //     }
    // }
    /**
     * Returns the single page of the application ( check the blade file SPA  )
     *  with the necessary data: app settings, favicon, themes and title of the page.
     * @return \Illuminate\Http\Response
     */
    public function SPA(Request $request)
    {
        if (!file_exists(storage_path('installed'))) {
            return redirect()->route('installer');
        }
        // if (!Session::get('current_ip')) {
        //     self::updateLocation();
        // } else {
        //     self::checkSessionTime();
        // }
        $this->defaultMetaTags();

        $settings = Setting::getPublicSettings();
        $themes = Cache::remember('themes', 7200, function () {
            return json_decode(Setting::get('themes'), true);
        });

        // if ($this->isACrawler()) {
            $this->applyMetaTags();
        // }

        return view('SPA', [
            'settings' => $settings,
            'themes' => $themes,
            'locale' => Language::default()
        ]);


        // return view('layouts.index');

    }
    /**
     * Send a contact email from a guest/user.
     * @return \Illuminate\Http\Response
     */
    public function sendContactEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|min:4',
            'message' => 'required'
        ]);
        Mail::to(Setting::get('contactEmailAddress'))->send(new contactMail($request->all()));
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    public function replaceVariables($titlePatten, $title,  $artist, $subject)
    {
        $site_name_replace  = str_replace('%site_name', Setting::get('appName'), $subject);
        $title_replace =  str_replace($titlePatten, $title, $site_name_replace);
        $artist_name_replace =  str_replace('%artist_name', $artist, $title_replace);

        return $artist_name_replace;
    }
}
