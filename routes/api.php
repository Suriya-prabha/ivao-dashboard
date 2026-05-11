<?php

use App\Http\Controllers\WhazzUpController;
use Illuminate\Support\Facades\Route;

Route::get('/summary', [WhazzUpController::class, 'summary']);

Route::get('/pilots', [WhazzUpController::class, 'pilots']);

Route::get('/airports', [WhazzUpController::class, 'airports']);

Route::get('/aircraft', [WhazzUpController::class, 'aircraft']);

Route::get('/atc', [WhazzUpController::class, 'atc']);