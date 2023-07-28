<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;

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

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('task/create', [TaskController::class, 'create']);
    Route::post('task/get', [TaskController::class, 'index']);
    Route::post('task/getByToken', [TaskController::class, 'show']);
    Route::post('user/logout', [UserController::class, 'logout']);
    Route::post('task/delete', [TaskController::class, 'destroy']);
    Route::post('task/update', [TaskController::class, 'update_task']);
    Route::post('task/update_status', [TaskController::class, 'update_status']);
    Route::post('task/favourite', [TaskController::class, 'favourite_update']);
    Route::post('task/fav_filter', [TaskController::class, 'fav_filter']);
    Route::post('task/status_filter', [TaskController::class, 'fav_status']);
    Route::post('task/delete_mul', [TaskController::class, 'delete_mul']);
});

Route::post('user/register', [UserController::class, 'register']);
Route::post('user/login', [UserController::class, 'login']);
