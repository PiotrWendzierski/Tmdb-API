<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\SerieController;
use App\Http\Controllers\GenreController;

Route::get('movies', [MovieController::class, 'index']);
Route::get('series', [SerieController::class, 'index']);
Route::get('genres', [GenreController::class, 'index']);
