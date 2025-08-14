<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Str;

// Route::get('test', function (){
//     // return new \App\Mail\OverdueReminder();

//     \Illuminate\Support\Facades\Mail::to('ray@mail.com')->send(
//         new \App\Mail\OverdueReminder()
//     );

//     return "Mail was sent";
// });

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::post('/tasks', [TaskController::class,'store'])->middleware('auth:api');
Route::get('/tasks', [TaskController::class,'index']);

Route::get('/tasks/completed', [TaskController::class, 'get_completed_task'])->middleware('auth:api');

Route::get('/tasks/overdue', [TaskController::class, 'overdue'])->middleware('auth:api');
Route::get('/tasks/overdue', [TaskController::class, 'overdue'])->middleware('auth:api');

Route::get('/tasks/ongoing', [TaskController::class, 'ongoing'])->middleware('auth:api');


Route::get('/tasks/{task}', [TaskController::class, 'show'])->middleware('auth:api');
Route::put('/tasks/{task}', [TaskController::class,'update'])->middleware('auth:api');
Route::delete('/tasks/{task}', [TaskController::class,'destroy']);
Route::get('/user/tasks', [TaskController::class,'user_tasks'])->middleware('auth:api');
Route::get('/tasks/{task}', [TaskController::class,'show']);
Route::get('/tasks/search/{search}', [TaskController::class, 'search'])->middleware('auth:api');
//Create middleware to ensure user seeing only his tasks when he search


Route::patch('/tasks/{task}/complete', [TaskController::class, 'completedTask'])->middleware('auth:api');
Route::patch('/tasks/{task}/status/undo', [TaskController::class, 'undoTask'])->middleware('auth:api');




Route::get('/reminder', [TaskController::class,'reminder']);