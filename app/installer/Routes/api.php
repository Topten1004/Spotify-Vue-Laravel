<?php

Route::group(['prefix' => 'installer', 'namespace' => 'App\installer\Controllers'], function() {
    Route::get('requirements', 'RequirementsController@requirements');
    Route::get('permissions', 'PermissionsController@permissions');

    Route::post('test-db-connection', 'DatabaseController@testDBConnection');

    Route::post('env', 'InstallationController@env');

    Route::post('migrate', 'InstallationController@migrate');

    Route::post('dependencies', 'InstallationController@dependencies');

    Route::post('translations', 'InstallationController@translations');

    Route::post('finish', 'InstallationController@finish');
});