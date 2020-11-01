<?php

use Illuminate\Support\Facades\Route;

Route::get('profile', '\App\Http\Controllers\UserController@profile')->name('profile');
Route::resource('users', 'UserController');
Route::post('users/{id}/restore', 'UserController@restore')->name('users.restore');
Route::post('json/filters/users', 'UserController@jsonFilters')->name('users.json.filters');
Route::get('json/users', 'UserController@indexJson')->name('users.json.index');
