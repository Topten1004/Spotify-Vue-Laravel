<?php


use Illuminate\Support\Facades\Route;

// this file contains the authentication routes

Route::get('email/verify/{id}', 'VerificationController@verify')->name('verification.verify'); // Make sure to keep this as your route name
Route::get('email/resend', 'VerificationController@resend')->name('verification.resend');

Route::post('password/forgot', 'ForgotPasswordController@sendPasswordResetLink');
Route::post('password/reset', 'ForgotPasswordController@callResetPassword');

Route::post('/login', 'AuthController@auth');
Route::post('login/{driver}/callback', 'AuthController@handleProviderCallback');

Route::post('/register', 'AuthController@register');
