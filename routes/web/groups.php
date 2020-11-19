<?php

use Illuminate\Support\Facades\Route;

Route::resource('groups', 'GroupController')->only(['store', 'update', 'destroy']);
