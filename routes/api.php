<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserBasicInfoController;
use App\Http\Middleware\CheckApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Server' => 'Server starting ....'];;
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware([CheckApiToken::class])->group(function () {

    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

 

    // new route add

    Route::post('/user/basic-info', [UserBasicInfoController::class, 'storeBasicInfo']);

    Route::post('/posts/add', [PostController::class, 'store']);
    Route::get('/posts', [PostController::class, 'index']);
    
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);

    // Route::get('/tags', [TagController::class, 'index']);
    // Route::post('/tags', [TagController::class, 'store']);


});
