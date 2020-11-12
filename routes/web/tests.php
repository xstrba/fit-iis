<?php

use Illuminate\Support\Facades\Route;

Route::resource('tests', 'TestController');
Route::post('tests/{id}/request-assistant', 'TestController@requestAssistant')->name('tests.request-assistant');
Route::post('tests/{id}/remove-assistant/{userId}', 'TestController@removeAssistant')->name('tests.remove-assistant');
Route::post('tests/{id}/accept-assistant/{userId}', 'TestController@acceptAssistant')->name('tests.accept-assistant');
Route::post('tests/{id}/restore', 'TestController@restore')->name('tests.restore');
Route::post('json/filters/tests', 'TestController@jsonFilters')->name('tests.json.filters');
Route::get('json/tests', 'TestController@indexJson')->name('tests.json.index');
