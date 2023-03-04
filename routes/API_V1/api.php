<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::name('')->group(__DIR__ . '/_auth.php');

Route::middleware('auth:api')
    ->group(__DIR__ . '/_user.php');

Route::middleware(['auth:api', 'scope:manage_own_content'])
    ->group(__DIR__ . '/_artist.php');

Route::name('admin.')->prefix('admin')
    ->middleware(['auth:api', 'scope:manage_own_content', 'scope:manage_everything'])
    ->group(__DIR__ . '/_admin.php');

Route::middleware('guest')
    ->group(__DIR__ . '/_guest.php');
