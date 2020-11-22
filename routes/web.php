<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get(\App\Providers\RouteServiceProvider::HOME, 'HomeController@index')->name('home');

$path = base_path('routes/web');
$files = \Illuminate\Support\Facades\File::files($path);

foreach ($files as $file) {
    /** @noinspection PhpIncludeInspection */
    require $file;
}

Route::get('media/{path}', 'MediaController@show')->name('media.show')->where('path', '(.*)?');
