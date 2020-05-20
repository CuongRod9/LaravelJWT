<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::group(['middleware' => ['jwt.verify']], function () {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('user', 'AuthController@user');
    Route::post('test', 'AuthController@testSearch');
// });