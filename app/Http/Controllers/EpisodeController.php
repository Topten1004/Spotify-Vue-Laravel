<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Episode;
use App\Exceptions\FEException;
use App\Http\Resources\EpisodeResource;
use FileManager;

class EpisodeController extends Controller
{
    /**
     * Update the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
        ]);

        $episode = Episode::find($request->id);

        if ($request->source_format === 'file') {
            if ($request->file('source')) {
                $source = FileManager::update($request->file('source'), $episode->source, '/audios/songs/');
                $file_name = $request->file('source')->getClientOriginalName();
                $file_size = $request->file_size;
                $episode->file_size = $file_size;
                $episode->file_name = $file_name;
                $episode->source = $source;
            } else if( !$episode->source ) {
                $request->validate([
                    'source' => 'required',
                ]);
            }
        } else if ($request->source_format === 'yt_video') {
            if ($request->source) {
                // delete audio file if it exists
                if ($episode->source_format === 'file') {
                    FileManager::delete($episode->source);
                }
                $source = json_encode($request->source);
                $file_name = null;
                $file_size = null;
                $episode->file_name = $file_name;
                $episode->file_size = $file_size;
                $episode->source = $source;
            } else if(!$episode->source ){
                $request->validate([
                    'source' => 'required',
                ]);
            }
        }

        $episode->title = $request->title;
        $episode->source_format = $request->source_format;
        $episode->description = $request->description;
        $episode->duration = $request->duration;

        $episode->save();

        return response()->json(new EpisodeResource($episode), 200);
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
            'title' => 'required|max:255|min:1|string',
        ]);

        if( $request->uploaded_by  === 'artist' ) {
            $available_space = auth()->user()->artist->available_disk_space;
            $used_space = auth()->user()->artist->used_disk_space();
            if (($request->file_size || 0) + $used_space > ($available_space * 1024 * 1024)) {
                throw new FEException('You have exceeded your space limit', '', 400);        
            }
        } else if ( $request->uploaded_by  === 'user' ) {
            $used_space = auth()->user()->used_disk_space();
            // checking the storage space given by the plan
            if ($sub = auth()->user()->active_subscription()->first()) {
                $user_plan = $sub->plan;
            }
            if (isSet($user_plan)) {
                $available_space = auth()->user()->available_disk_space > $user_plan->storage_space  ? auth()->user()->available_disk_space : $user_plan->storage_space;
            } else {
                $available_space = auth()->user()->available_disk_space;
            }
            if (($request->file_size || 0) + $used_space > ($available_space * 1024 * 1024)) {
                throw new FEException('You have exceeded your space limit', '', 400);        
            }
        }

        if( $request->source_format === 'file' ) {
            if ($request->file('source')) {
                $file_name = $request->file('source')->getClientOriginalName();
                $file_size = $request->file_size;
                $source = FileManager::store($request, '/audios/episodes/', 'source');
            } else {
                $request->validate([
                    'source' => 'required',
                ]);
            }
        } else if ( $request->source_format === 'yt_video' ) {
            if( $request->source ) {
                $source = json_encode($request->source); // source column has type JSON
                $file_name = null;
                $file_size = null;
            } else {
                $request->validate([
                    'source' => 'required',
                ]);
            }
        } else if ( $request->source_format === 'audio_url' ) {
            $request->validate([
                'source' => 'required|url',
            ]);

            $url = $request->source;

            if( isset($request->saveFileFromURL) ) {
                $ch = curl_init($url);

                // Use basename() function to return
                // the base name of file 
                $file_name = basename($url);
                
                // Save file into file location
                $save_file_loc = "storage" . '/audios/episodes/' . $file_name;
            
                // Open file 
                $fp = fopen($save_file_loc, 'wb');
                
                // It set an option for a cURL transfer
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                
                // Perform a cURL session
                curl_exec($ch);
                
                // Closes a cURL session and frees all resources
                curl_close($ch);
                
                // Close file
                fclose($fp);
    
                $file_size = 0;
                
                $source = $save_file_loc;
            } else {
                $source = $url;
            }
        }

        $episode = new Episode();

        $episode->title = $request->title;
        $episode->description = $request->description;
        // $episode->uploaded_by = $request->uploaded_by;
        // $episode->artist_id = $request->artist_id;
        // $episode->user_id = auth()->user()->id;
        $episode->podcast_id = $request->podcast_id;
        $episode->source_format = $request->source_format;
        $episode->file_name = $file_name;
        $episode->source = $source;
        $episode->file_size = $file_size;
        $episode->duration = $request->duration;

        $episode->save();

        return response()->json(new EpisodeResource($episode), 201);
    }
        /**
     * Download a episode.
     *
     * @param  \App\episode  $id
     * @return \Illuminate\Http\Response
     */
    function download($id)
    {
        $episode = Episode::find($id);
        $file = FileManager::download($episode);
        $episode->download_count++;
        $episode->save();
        return $file;
    }

    /**
     * Remove the specified resource from storage.
     * @param  \App\Episode  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Episode::find($id)->delete();
        return response()->json(['message' => 'SUCCESS'], 200);
    }
}
