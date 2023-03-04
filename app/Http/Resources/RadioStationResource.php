<?php

namespace App\Http\Resources;

use App\Exceptions\FEException;
use App\Helpers\Radio\Radio;
use App\Helpers\FileManager;
use App\RadioStation;
use Exception;
use Illuminate\Http\Resources\Json\JsonResource;

class RadioStationResource extends JsonResource
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
            'type' => 'radio-station',
            'id' => $this->id,
            'name' => $this->name,
            'cover' => FileManager::asset_path($this->cover),
            'streamEndpoint' => $this->streamEndpoint,
            'highlight' => $this->highlight == "0" ? false : true,
            'proxy' => $this->proxy == "0" ? false : true,
            'serverType' => $this->serverType,
            'statsSource' => $this->statsSource,
            'serverURL' => $this->serverURL,
            'serverID' => $this->serverID,
            'serverMount' => $this->serverMount,
            'serverUsername' => $this->serverUsername,
            'serverPassword' => $this->serverPassword,
            'stats' => $this->statsSource === 'server' ? $this->getStats(): '',
            'statsEndpoint' => $this->statsEndpoint,
            'interval' => $this->interval,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function getStats()
    {
        $radio = new Radio();
        try {
            $stats = $radio->parse($this, $this->serverType);
            return $stats;
        } catch(Exception $e) {
            RadioStation::find($this->id)->delete();
            throw new FEException('Failed to connect to stream. Please make sure your data is correct.', $e->getMessage(), 500);
        }
    }
}
