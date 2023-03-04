<?php

namespace App\Http\Controllers;

use App\NavigationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NavigationItemController extends Controller
{
    /**
     * save the navigation items setting submitted by the admin.
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function saveNavigationItems(Request $request)
    {
        NavigationItem::truncate();
        $rank = 0;
        foreach ($request->items as $item) {
            NavigationItem::create([
                'name' => $item['name'],
                'icon' => $item['icon'],
                'path' => $item['path'],
                'visibility' => $item['visibility'],
                'custom' => $item['custom'],
                'navigatesTo' => $item['navigatesTo'],
                'page_id' => isSet($item['page_id']) ? $item['page_id'] : null,
                'rank' => $rank
            ]);
            $rank++;
        }
        Cache::put('navigation-items', NavigationItem::orderBy('rank')->with('page')->get() ,15000);
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * reset the navigation items setting submitted by the admin.
     * @return \Illuminate\Http\Response
     */
    public function resetNavigationItems()
    {
        NavigationItem::truncate();
        // seeding the default Navigation Items
        NavigationItem::create([
            'name' => 'Home',
            'icon' => 'home',
            'visibility' => 1,
            'page_id' => 1,
            'custom' => 1,
            'rank' => 1,
            'path' => '/home',
            'navigatesTo' => 'Custom page'
        ]);
        NavigationItem::create([
            'name' => 'Browse',
            'icon' => 'compass',
            'visibility' => 1,
            'custom' => 0,
            'rank' => 2,
            'path' => '/browse'
        ]);
        NavigationItem::create([
            'name' => 'Podcasts',
            'icon' => 'microphone',
            'visibility' => 1,
            'custom' => 0,
            'rank' => 3,
            'path' => '/podcasts'
        ]);
        NavigationItem::create([
            'name' => 'Library',
            'icon' => 'music-box-multiple',
            'visibility' => 1,
            'custom' => 0,
            'rank' => 4,
            'path' => '/library/my-songs'
        ]);
        NavigationItem::create([
            'name' => 'Upload',
            'icon' => 'cloud-upload',
            'visibility' => 1,
            'custom' => 0,
            'rank' => 5,
            'path' => '/upload-song'
        ]);
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    /**
     * Get all the navigation items.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Cache::get('navigation-items', function() {
            return NavigationItem::orderBy('rank')->with('page')->get();
        });
    }
}
