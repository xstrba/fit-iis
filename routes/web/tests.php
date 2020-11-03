<?php

use Illuminate\Support\Facades\Route;

Route::resource('tests', 'TestController');
Route::post('tests/{id}/restore', 'TestController@restore')->name('users.restore');
Route::post('json/filters/tests', 'TestController@jsonFilters')->name('users.json.filters');
Route::get('json/tests', 'TestController@indexJson')->name('users.json.index');
