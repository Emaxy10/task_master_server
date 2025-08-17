<?php

namespace App\Http\Controllers;

use App\Models\SubTask;
use App\Models\Task;
use Illuminate\Http\Request;

class SubTaskController extends Controller
{
    /**
     * Display a listing of subtasks for a given task.
     */
    public function index(Task $task)
    {
        return response()->json($task->subTasks);
    }

    /**
     * Store a newly created subtask.
     */
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'end_date' => 'nullable|date',
        ]);

        $subTask = $task->subTasks()->create($validated);

        return response()->json($subTask, 201);
    }

    /**
     * Display a single subtask.
     */
    public function show(Task $task, SubTask $subTask)
    {
        if ($subTask->task_id !== $task->id) {
            return response()->json(['error' => 'SubTask does not belong to this task'], 403);
        }

        return response()->json($subTask);
    }

    /**
     * Update a subtask.
     */
    public function update(Request $request, Task $task, SubTask $subTask)
    {
        if ($subTask->task_id !== $task->id) {
            return response()->json(['error' => 'SubTask does not belong to this task'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'boolean',
            'end_date' => 'nullable|date',
        ]);

        $subTask->update($validated);

        return response()->json($subTask);
    }

    /**
     * Remove a subtask.
     */
    public function destroy(Task $task, SubTask $subTask)
    {
        if ($subTask->task_id !== $task->id) {
            return response()->json(['error' => 'SubTask does not belong to this task'], 403);
        }

        $subTask->delete();

        return response()->json(null, 204);
    }
}
