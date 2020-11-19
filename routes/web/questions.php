<?php

use Illuminate\Support\Facades\Route;

Route::resource('questionStudents', 'QuestionSolutionController')
    ->only(['update']);
