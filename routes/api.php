<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BlogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('registration', [AuthController::class, 'registration']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Authenticated Routes in api.php
    Route::get('/user', function () {
        return auth()->user();
    });
    Route::post('logout', [AuthController::class, 'logout']);

    //Blogs
    Route::post('create', [BlogController::class, 'create']);
    Route::post('update/{id}', [BlogController::class, 'update']);
    Route::get('read/{id}', [BlogController::class, 'read']);
    Route::delete('delete/{id}', [BlogController::class, 'delete']);
    Route::get('blog-published/{id}', [BlogController::class, 'BlogPublished']);
    Route::get('all-blogs', [BlogController::class, 'AllBlogs']);
    Route::get('list', [BlogController::class, 'list']);
    Route::post('create-category', [BlogController::class, 'createCategory']);


});
