<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubTaskController;
use App\Models\Role;
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

Route::get('/tasks/team/members', [UserController::class, 'getTeamMembers'])->middleware('auth:api');

Route::get('/tasks/completed', [TaskController::class, 'get_completed_task'])->middleware('auth:api');

//Route::get('/tasks/overdue', [TaskController::class, 'overdue'])->middleware('auth:api');
Route::get('/tasks/overdue', [TaskController::class, 'overdue'])->middleware('auth:api');

Route::get('/tasks/ongoing', [TaskController::class, 'ongoing'])->middleware('auth:api');

//get assigned task
Route::get('/tasks/assigned', [TaskController::class, 'getAssignedTask'])->middleware('auth:api');



Route::get('/tasks/{task}', [TaskController::class, 'show'])->middleware('auth:api');
Route::put('/tasks/{task}', [TaskController::class,'update'])->middleware('auth:api');
Route::delete('/tasks/{task}', [TaskController::class,'destroy'])
->middleware('auth:api')
->middleware('role:admin')
->middleware('perm:delete_task');


//Assign Task
Route::post('/tasks/{task}/assign', [TaskController::class, 'assignTask'])->middleware('auth:api');


Route::get('/user/tasks', [TaskController::class,'user_tasks'])->middleware('auth:api');
Route::get('/tasks/{task}', [TaskController::class,'show']);
Route::get('/tasks/search/{search}', [TaskController::class, 'search'])->middleware('auth:api');
//Create middleware to ensure user seeing only his tasks when he search

//get Email
Route::get('/tasks/user/{search}/email', [UserController::class, 'searchEmail'])->middleware('auth:api');
//Add member
Route::patch('/tasks/user/add/{user}/member', [UserController::class, 'addTeamMember'])->middleware('auth:api');

// Remove member
Route::delete('/tasks/team/members/{id}', [UserController::class, 'removeMember'])->middleware('auth:api');




Route::patch('/tasks/{task}/complete', [TaskController::class, 'completedTask'])->middleware('auth:api');
Route::patch('/tasks/{task}/status/undo', [TaskController::class, 'undoTask'])->middleware('auth:api');


// Route::middleware('auth:api')->group(function () {
//     Route::apiResource('tasks.subtasks', SubTaskController::class);
// });


// This automatically gives you RESTful nested routes like:

// GET /tasks/{task}/subtasks → index subtasks of a task

// POST /tasks/{task}/subtasks → create subtask

// GET /tasks/{task}/subtasks/{subtask} → show subtask

// PUT /tasks/{task}/subtasks/{subtask} → update subtask

// DELETE /tasks/{task}/subtasks/{subtask} → delete subtask


Route::middleware(['auth:api', 'role:admin,supervisor', 'perm:delete_subtask'])->group(function () {
    // GET /tasks/{task}/subtasks → index subtasks of a task
    Route::get('tasks/{task}/subtasks', [SubTaskController::class, 'index']);

    // POST /tasks/{task}/subtasks → create subtask
    Route::post('tasks/{task}/subtasks', [SubTaskController::class, 'store']);

    // GET /tasks/{task}/subtasks/{subtask} → show subtask
    Route::get('tasks/subtasks/{subtask}', [SubTaskController::class, 'show']);

    // PUT /tasks/{task}/subtasks/{subtask} → update subtask
    Route::put('/tasks/subtasks/{subtask}', [SubTaskController::class, 'update']);
    //Route::patch('tasks/subtasks/{subtask}', [SubTaskController::class, 'update']); // optional

    // DELETE /tasks/{task}/subtasks/{subtask} → delete subtask
    Route::delete('/tasks/subtasks/{subtask}', [SubTaskController::class, 'destroy']);
});