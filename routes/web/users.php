<?php

use Illuminate\Support\Facades\Route;

Route::get('profile', '\App\Http\Controllers\UserController@profile')->name('profile');
Route::resource('users', 'UserController');
Route::post('users/{id}/restore', 'UserController@restore')->name('users.restore');
Route::get('json/users', 'UserController@indexJson')->name('users.json.index');
