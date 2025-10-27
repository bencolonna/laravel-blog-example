<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{postId}', [PostController::class, 'show']);

Route::middleware('jwt')->group(function () {
    Route::post('/posts', [PostController::class, 'create']);
    Route::put('/posts/{postId}', [PostController::class, 'update']);
    Route::delete('/posts/{postId}', [PostController::class, 'delete']);
});

Route::get('/posts/{postId}/comments', [CommentController::class, 'index'])->middleware('has-post');
Route::post('/posts/{postId}/comments', action: [CommentController::class, 'create'])->middleware('has-post');

Route::middleware('jwt')->group(function () {
    Route::put('/posts/{postId}/comments/{commentId}', [CommentController::class, 'update'])->middleware('has-post');
    Route::delete('/posts/{postId}/comments/{commentId}', [CommentController::class, 'delete'])->middleware('has-post');
});

Route::post('/users', [UserController::class, 'create']);
Route::get('/users/{userId}', [UserController::class, 'show']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('jwt')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'show']);
    Route::put('/me', [AuthController::class, 'update']);
});
