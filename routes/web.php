<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
	//dd('hi');
    return view('welcome');
});


Route::get('/create-storage-link', function () {
    Artisan::call('storage:link');
    return 'Storage link created!';
});


Route::get('/config-clear', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    return 'Configuration cache cleared!';
});