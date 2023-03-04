<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = ['name', 'locale', 'status', 'isDefault', 'isDeletable'];

    static function default() {
        $default = Language::where('isDefault', 1)->first();
        if( !$default ) {
            Language::where('locale', 'en')->update(['isDefault' => 1]);
        }
        return  $default ? $default->locale : 'en';
    }
}
