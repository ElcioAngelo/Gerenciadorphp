<?php

use Illuminate\Support\Facades\Route;

// * Rota inicial do servidor
Route::get('/', function () {
    return view('index');
});

Route::get('/computer', function () {
    return view('computer');
});





