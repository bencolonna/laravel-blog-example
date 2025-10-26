<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/posts', [PostController::class, 'index']);
Route::post('/posts', [PostController::class, 'create']);
Route::get('/posts/{postId}', [PostController::class, 'show']);
Route::put('/posts/{postId}', [PostController::class, 'update']);
Route::delete('/posts/{postId}', [PostController::class, 'delete']);
