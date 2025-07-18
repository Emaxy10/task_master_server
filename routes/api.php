<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Str;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::post('/tasks', [TaskController::class,'store'])->middleware('auth:api');
Route::get('/tasks', [TaskController::class,'index']);
Route::put('/tasks/{task}', [TaskController::class,'update'])->middleware('auth:api');
Route::delete('/tasks/{task}', [TaskController::class,'destroy']);
Route::get('/user/tasks', [TaskController::class,'user_tasks'])->middleware('auth:api');
Route::get('/tasks/{task}', [TaskController::class,'show']);

//Route::get('/reminder', [TaskController::class,'reminder']);