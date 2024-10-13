<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::middleware(['cors'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->patch('/profile', [UserController::class, 'updateProfile']);
Route::middleware('auth:sanctum')->get('/profile', [UserController::class, 'getProfile']);
Route::get('/posts', [PostController::class, 'getAllPosts']);
Route::middleware('auth:sanctum')->get('/my-posts', [PostController::class, 'getPostsByLoggedInUser']);
Route::middleware('auth:sanctum')->patch('/likes/{posts}', [PostController::class, 'likePost']);
Route::middleware('auth:sanctum')->post('/posts', [PostController::class, 'createPost']);
Route::middleware('auth:sanctum')->patch('/posts/{post}', [PostController::class, 'updatePost']);
Route::middleware('auth:sanctum')->delete('/posts/{post}', [PostController::class, 'deletePost']);
Route::middleware('auth:sanctum')->post('/comments', [CommentController::class, 'createComment']);
Route::middleware('auth:sanctum')->patch('/comments/{commentId}', [CommentController::class, 'updateComment']);
Route::middleware('auth:sanctum')->post('/comments/{commentId}/reply', [CommentController::class, 'replyToComment']);
