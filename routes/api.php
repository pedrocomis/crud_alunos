<?php

use App\Http\Controllers\AlunoController;
use Illuminate\Support\Facades\Route;

Route::apiResource('alunos', AlunoController::class);