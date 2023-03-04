<?php

use App\Setting;
use Illuminate\Support\Facades\Route;



// single page application main route


Route::get('/update/{version}', "UpgradeController@update");

Route::get('{any}', "HomeController@SPA")->where('any', '^(?!api).*$');

