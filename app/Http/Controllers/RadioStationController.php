<?php

namespace App\Http\Controllers;

use App\Exceptions\FEException;
use App\Helpers\FileManager;
use App\Helpers\Radio\Radio;
use App\Http\Resources\RadioStationResource;
use App\RadioStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Traits\Search;
use Exception;

class RadioStationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return RadioStationResource::collection(RadioStation::all());
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
            'name' => 'required|min:1|string',
            'streamEndpoint' => 'required'
        ]);

        $cover = FileManager::store($request, '/covers/radio-stations/', 'cover');

        $radioStation = RadioStation::create([
            'name' => $request->name,
            'cover' => $cover,
            'streamEndpoint' => $request->streamEndpoint,
            'serverType' => $request->serverType,
            'serverURL' => $request->serverURL,
            'serverID' => $request->serverID,
            'serverMount' => $request->serverMount,
            'serverUsername' => $request->serverUsername,
            'serverPassword' => $request->serverPassword,
            'statsEndpoint' => $request->statsEndpoint,
            'statsSource' => $request->statsSource,
            'highlight' => $request->highlight,
            'proxy' => $request->proxy,
            'interval' => intval($request->interval),
        ]);

        try {
            return new RadioStationResource($radioStation);
        } catch(Exception $e) {
            throw new FEException('Failed to connect to stream. Please make sure your data is correct.', $e->getMessage(), 500);
        }
        return response()->json(['message' => 'SUCCESS'], 200);
    }
    // getting the stats using proxy
    public function getMetaDataProxy($station_id) {
        $station = RadioStation::find($station_id);
        $response =  Http::get($station->endpoint)->json();
        if( $response )
        return response()->json($response);
        else
        return response()->json([], 200);
    }
    // getting the stats from server with auth
    public function getMetaDataServer($station_id) {
        $station = RadioStation::find($station_id);
        $radio = new Radio();
        $stats = $radio->parse($station);
        return response()->json($stats);
    }
    /**
     * Retrieve the highlighted streams
     */
    public function highlights()
    {
        return RadioStationResource::collection(RadioStation::where('highlight', 1)->get());
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RadioStation  $radioStation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:1|string',
            'streamEndpoint' => 'required'
        ]);

        $radioStation = RadioStation::find($id);


        if ($request->file('cover')) {
            $radioStation->cover = FileManager::update($request->file('cover'), $radioStation->cover, '/covers/radio-stations/');
        }

        $radioStation->name = $request->name;
        $radioStation->streamEndpoint =  $request->streamEndpoint;
        $radioStation->serverType =  $request->serverType;
        $radioStation->serverURL =  $request->serverURL;
        $radioStation->serverMount =  $request->serverMount;
        $radioStation->serverID =  $request->serverID;
        $radioStation->serverUsername =  $request->serverUsername;
        $radioStation->serverPassword =  $request->serverPassword;
        $radioStation->statsSource =  $request->statsSource;
        $radioStation->statsEndpoint =  $request->statsEndpoint;
        $radioStation->highlight =  $request->highlight;
        $radioStation->proxy =  $request->proxy;
        $radioStation->interval =  intval($request->interval);

        $radioStation->save();
        return response()->json(['message' => 'SUCCESS'], 200);
    }

    public function matchRadioStations()
    {
        $engine = request()->query('engine');
        $keyword = request()->query('query');

        return Search::radioStations($keyword, $engine);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RadioStation  $radioStation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $radioStation = RadioStation::find($id);
        $radioStation->delete();

        return response()->json(['message' => 'SUCCESS'], 200);
    }
}
