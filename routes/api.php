<?php

use App\Http\Controllers\AuthUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//register route
Route::post('/user/register', [AuthUserController::class, 'register']);

//login route
Route::post('user/login', [AuthUserController::class, 'login']);

//logout route
Route::post('user/logout', [AuthUserController::class, 'logout'])->middleware('auth:api');

//update route
Route::post('/user/{id}', [AuthUserController::class, 'update'])->middleware('auth:api');

//testing route
Route::get('/user/test', function () {
    return response(['success' => 'Successful']);
})->middleware('hasToken');
