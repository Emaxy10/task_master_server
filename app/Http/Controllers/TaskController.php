<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Mail\TaskReminderMail;
use App\Mail\OverdueReminder;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Queue\RedisQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

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

        $user = auth::user();

        $task = Task::create($data);

        //Send mail
        // Mail::to($user)->send(
        // new OverdueReminder($task)
        // );

         Mail::to($user)->queue(
        new OverdueReminder($task)
        );

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
         $task->is_completed = true;
         return $task->save();
    }

    public function undoTask(Task $task){
        //update pending task to completed
         $task->status = "pending";
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

    public function search($search){

        $auth_user = Auth::user();
        // $tasks = Task::where('title', 'like', "%$search%")
        //             ->orWhere('description', 'like', "%$search%")
        //             ->get();

        //             return response()->json(
        //                  $tasks
        //             );


/** @var \App\Models\User $auth_user */
        $tasks = $auth_user->tasks()
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            })
            ->get();

             return response()->json(
                         $tasks
                    );
    }


    public function get_completed_task(){
         $auth_user = Auth::user();

         /** @var \App\Models\User $auth_user */
            $tasks = $auth_user->tasks()
            ->where('is_completed', true)
            ->where('status', 'completed')
            ->get();

            return response()->json(
                $tasks
            );

    }

    public function overdue(){
         $auth_user = Auth::user();

         /** @var \App\Models\User $auth_user */
            $tasks = $auth_user->tasks()
             ->where('is_completed', false)
            ->whereDate('end_date', '<', Carbon::today()) // end_date after today
            ->orderBy('end_date', 'asc') // soonest first
            ->get();

            return response()->json(
                $tasks
            );
    }

       public function ongoing(){
         $auth_user = Auth::user();

         /** @var \App\Models\User $auth_user */
            $tasks = $auth_user->tasks()
             ->where('is_completed', false)
             ->where('status', 'pending')
            ->whereDate('end_date', '>', Carbon::today()) 
            ->get();

            return response()->json(
                $tasks
            );
    }



}
