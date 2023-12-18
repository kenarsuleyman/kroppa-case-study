<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\LeaderboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('start_game', [GameController::class, 'start']);
Route::post('end_game', [GameController::class, 'end']);
Route::get('leaderboard', [LeaderboardController::class, 'show']);
