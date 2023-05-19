<?php

use App\Http\Controllers\EventController;
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

Route::controller(EventController::class)->group(function () {
    Route::post('/events/', 'create');
    Route::get('/events/', 'show');
    Route::post('/events/{event}', 'edit');
    Route::delete('/events/{event}', 'delete');
    Route::post('/events/{event}/complete', 'complete');
});
