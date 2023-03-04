<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RadioStation extends Model
{
    protected $fillable = [
        'name',
        'cover',
        'streamEndpoint',
        'interval',
        'proxy',
        'serverID',
        'serverMount',
        'serverURL',
        'serverUsername',
        'serverPassword',
        'statsEndpoint',
        'serverType',
        'statsSource',
        'highlight'
    ];

    public function plays()
    {
        return $this->hasMany('App\Play', 'content_id')->where('plays.content_type', '=', 'radio-station');
    }
}
