<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Mail\TaskReminderMail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Queue\RedisQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $tasks = Task::all();

        return response()->json([
            "task" => $tasks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        //
        $data = $request->validated();
        $data['user_id'] = Auth::id(); // or auth()->id()

        $task = Task::create($data);

        return response()->json([
            "task" => $task,
            "status" =>"success",
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
        return response()->json($task);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function user_tasks()
    {
        //
         $user = Auth::user(); // Get full user model
         $tasks = $user->tasks; // Eager load related tasks

         return response()->json([
            "tasks"=> $tasks,
         ]);
    }

    public function completedTask(Task $task){
        //update pending task to completed
         $task->status = "completed";
         return $task->save();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        //
        // $task->update($request->all());

        $task->update($request->validated());

        return response()->json([
            'message' => 'Task updated successfully.',
            'task' => $task
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
        return $task->delete();
    }

    public function reminder(){
        $tasks = Task::whereDate('due_date', today())->
                  orWhere('status', 'pending')
                  ->with('user')->get();
       // dd($tasks);
        foreach( $tasks as $task ){
            $user = $task->user;
            if($user){
                 Mail::to($user->email)->send(new TaskReminderMail($task));
            }
        }
    }
}
