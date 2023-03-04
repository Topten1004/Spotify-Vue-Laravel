<?php

namespace App\Http\Controllers;

use App\Page;
use App\Section;

use App\Http\Resources\PageResource;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PageResource::collection(Page::where('id','!=' ,1)->where('name','!=' ,'subscription')->get());
    }
    /**
     * Display a listing of the resource ( without the sections data ).
     *
     * @return \Illuminate\Http\Response
     */
    public function indexInfosOnly()
    {
        return Page::where('id','!=',1)->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:pages',
            'title' => 'required|unique:pages',
            'path' => 'required|unique:pages'
        ]);

        $page =  Page::create([
            'name' => $request->name,
            'title' => $request->title,
            'content' => $request->content,
            'showLinkOnTheRightSidebar' => $request->showLinkOnTheRightSidebar,
            'blank' => $request->blank,
            'description' => $request->description,
            'path' => $request->path,
            'icon' => isset($request->icon) ? $request->icon : 'music-circle'
        ]);

        return response()->json($page, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new PageResource(Page::find($id));
    }
    /**
     * Display the specified resource based on the given path (URL).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showPageByPath(Request $request)
    {
        $queries = $request->query();
        $page = Page::where('path', 'like', '%' . $queries['path'] . '%')->first();
        if ($page)
            return new PageResource($page);
        else
            return response()->json(['message' => 'NOT_FOUND'], 404);
    }

    /**
     * Update the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:pages,name,' . $id,
            'path' => 'required|unique:pages,path,' . $id,
            'title' => 'required|unique:pages,title,' . $id,
        ]);

        $page = Page::find($id);

        $page->name = $request->name;
        $page->blank = $request->blank;
        $page->content = $request->content;
        $page->showLinkOnTheRightSidebar = $request->showLinkOnTheRightSidebar;
        $page->title = $request->title;
        $page->description = $request->description;
        $page->path = $request->path;
        $page->icon = isset($request->icon) ? $request->icon : 'music-circle';

        $page->save();
        $rank = 0;
        foreach (json_decode($request->sections) as $ranked_section) {
            $section = Section::find($ranked_section->id);
            $section->rank = $rank;
            $section->save();
            $rank = $rank + 1;
        }

        return response()->json(['message' => 'SUCCESS'], 200);
    }

    public function rightSidebarPages() {
        $pages = Page::where('blank', 1)->orWhere('showLinkOnTheRightSidebar', 1)->get();
        return $pages;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Page::find($id)->delete();

        return response()->json(['message' => 'SUCCESS'], 200);
    }
}
