<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ApiController::class, 'login']);
Route::post('/register', [ApiController::class, 'register']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/short', [ApiController::class, 'short']);
});
