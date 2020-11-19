<?php

use Illuminate\Support\Facades\Route;

Route::resource('tests', 'TestController');
Route::post('tests/{id}/restore', 'TestController@restore')->name('tests.restore');
Route::post('json/filters/tests', 'TestController@jsonFilters')->name('tests.json.filters');
Route::get('json/tests', 'TestController@indexJson')->name('tests.json.index');

// users playing tests
Route::post('tests/{id}/start', 'TestSolutionController@start')->name('tests.start');
Route::get('tests/{id}/solution', 'TestSolutionController@solution')->name('tests.solution');
Route::post('tests/{id}/finish', 'TestSolutionController@finish')->name('tests.finish');

// students
Route::post('json/filters/tests/{id}/students', 'TestStudentsController@jsonFilters')->name('tests.students.json.filters');
Route::get('json/tests/{id}/students', 'TestStudentsController@indexJson')->name('tests.students.json.index');

Route::post('tests/{id}/request-student', 'TestStudentsController@request')->name('tests.request-student');
Route::post('tests/{id}/remove-student/{userId}', 'TestStudentsController@remove')->name('tests.remove-student');
Route::post('tests/{id}/accept-student/{userId}', 'TestStudentsController@accept')->name('tests.accept-student');

// assistants
Route::post('tests/{id}/request-assistant', 'TestAssistantsController@requestAssistant')->name('tests.request-assistant');
Route::post('tests/{id}/remove-assistant/{userId}', 'TestAssistantsController@removeAssistant')->name('tests.remove-assistant');
Route::post('tests/{id}/accept-assistant/{userId}', 'TestAssistantsController@acceptAssistant')->name('tests.accept-assistant');

// my tests
Route::get('my-tests', 'MyTestsController@index')->name('tests.my');
Route::post('json/filters/my-tests', 'MyTestsController@jsonFilters')->name('tests.my.json.filters');
Route::get('json/my-tests', 'MyTestsController@indexJson')->name('tests.my.json.index');

// solution
Route::get('tests/{id}/solution/{userId}', 'TestSolutionController@usersSolution')->name('tests.solution.users');
