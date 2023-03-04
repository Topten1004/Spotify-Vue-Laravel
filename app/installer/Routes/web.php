<?php

Route::group(['namespace' => 'App\installer\Controllers', 'middleware' => ['web', 'install']], function () {
    Route::get('/install', 'WelcomeController@installer')->name('installer');

//     Route::get('environment/wizard', [
//         'as' => 'environmentWizard',
//         'uses' => 'EnvironmentController@environmentWizard',
//     ]);

//     Route::post('environment/saveWizard', [
//         'as' => 'environmentSaveWizard',
//         'uses' => 'EnvironmentController@saveWizard',
//     ]);

//     Route::post('environment/createUserAndFinish', [
//         'as' => 'createAdminAndFinish',
//         'uses' => 'DatabaseController@validateSuperAdminData',
//     ]);

//     Route::get('environment/classic', [
//         'as' => 'environmentClassic',
//         'uses' => 'EnvironmentController@environmentClassic',
//     ]);

//     Route::post('environment/saveClassic', [
//         'as' => 'environmentSaveClassic',
//         'uses' => 'EnvironmentController@saveClassic',
//     ]);

//     Route::get('requirements', [
//         'as' => 'requirements',
//         'uses' => 'RequirementsController@requirements',
//     ]);

//     Route::get('permissions', [
//         'as' => 'permissions',
//         'uses' => 'PermissionsController@permissions',
//     ]);

//     Route::get('create-admin', [
//         'as' => 'createAdmin',
//         'uses' => 'DatabaseController@createAdminPage',
//     ]);


//     Route::get('database', [
//         'as' => 'database',
//         'uses' => 'DatabaseController@database',
//     ]);

//     Route::get('final', [
//         'as' => 'final',
//         'uses' => 'FinalController@finish',
//     ]);
// });

// Route::group(['prefix' => 'update', 'as' => 'LaravelUpdater::', 'namespace' => 'App\installer\Controllers', 'middleware' => 'web'], function () {
//     Route::group(['middleware' => 'update'], function () {
//         Route::get('/', [
//             'as' => 'welcome',
//             'uses' => 'UpdateController@welcome',
//         ]);

//         Route::get('overview', [
//             'as' => 'overview',
//             'uses' => 'UpdateController@overview',
//         ]);

//         Route::get('database', [
//             'as' => 'database',
//             'uses' => 'UpdateController@database',
//         ]);
//     });

//     // This needs to be out of the middleware because right after the migration has been
//     // run, the middleware sends a 404.
//     Route::get('final', [
//         'as' => 'final',
//         'uses' => 'UpdateController@finish',
//     ]);
});
