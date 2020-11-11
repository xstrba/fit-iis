<?php

use Illuminate\Support\Facades\Route;

Route::resource('tests', 'TestController');
Route::post('tests/{id}/restore', 'TestController@restore')->name('tests.restore');
Route::post('json/filters/tests', 'TestController@jsonFilters')->name('tests.json.filters');
Route::get('json/tests', 'TestController@indexJson')->name('tests.json.index');
