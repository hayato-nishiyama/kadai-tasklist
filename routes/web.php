<<?php



Route::get('/test', 'TasksController@index');

Route::resource('tasks', 'TasksController');