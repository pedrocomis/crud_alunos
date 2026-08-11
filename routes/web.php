<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('crud_alunos');
});

Route::get('/alunos', function () {
    return view('crud_alunos');
});
