<?php

use Illuminate\Support\Facades\Route;

// * Rota inicial do servidor
Route::get('/', function () {
    return view('index');
});

Route::get('/computer', function () {
    return view('computer');
});

Route::get('/sector', function() {
    return view('sector');
});


// routes/web.php
Route::get('/', function () {
    return view('index'); // carrega resources/views/index.blade.php
});


use App\Http\Controllers\SetoresController;

Route::get('/setores/listar', [SetoresController::class, 'listarSetores']);
Route::post('/setores/salvar', [SetoresController::class, 'salvarSetor']);


