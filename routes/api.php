<?php

use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
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

//current user route
Route::get('/user', [AuthUserController::class, 'user'])->middleware('auth:api');

//all users
Route::get('/user/all', [AuthUserController::class, 'all'])->middleware('auth:api');


//post
Route::post('/post/upload', [PostController::class, 'post'])->middleware('auth:api');

//get post belong to current user
Route::get('/post', [PostController::class, 'index'])->middleware('auth:api');

//like route
Route::post('/post/{id}/like', [LikeController::class, 'like'])->middleware('auth:api');

//unlike route
Route::post('/post/{id}/unlike', [LikeController::class, 'unlike'])->middleware('auth:api');

//get all likes from post
Route::get('/post/{id}/like', [LikeController::class, 'getPostLike'])->middleware('auth:api');

//comment on post
Route::post('/post/{id}/comment', [CommentController::class, 'comment'])->middleware('auth:api');

//delete comment
Route::delete('/post/{pid}/{cid}', [CommentController::class, 'removeComment'])->middleware('auth:api');

//update comment
Route::post('/post/comment/{id}', [CommentController::class, 'update'])->middleware('auth:api');

//get comments on post
Route::get('/post/{pid}', [CommentController::class, 'getComment'])->middleware('auth:api');
