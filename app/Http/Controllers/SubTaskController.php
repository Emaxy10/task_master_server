<?php

namespace App\Http\Controllers;

use App\Models\SubTask;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubTaskController extends Controller
{
    /**
     * Display a listing of subtasks for a given task.
     */
    public function index(Task $task)
    {
        return response()->json($task->subTasks);
    }

  

    // Store subtask
    public function store(Request $request, Task $task)
    {
        // Validate array of subtasks
        $validated = $request->validate([
            'subtasks' => 'required|array',
            'subtasks.*.title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sub_tasks', 'title')->where(column: function ($query) use ($task) {
                    return $query->where('task_id', $task->id);
                }),
            ],
            'subtasks.*.description' => 'nullable|string',
            'subtasks.*.end_date' => [
                'nullable',
                'date',
                'after_or_equal:today', // ✅ cannot be in the past
                function ($attribute, $value, $fail) use ($task) {
                    if ($task->end_date && $value > $task->end_date) {
                        $fail("The {$attribute} cannot be later than the parent task's end date ({$task->end_date}).");
                    }
                },
            ],
        ]);
        

        $createdSubtasks = [];

        foreach ($validated['subtasks'] as $subtaskData) {
            $createdSubtasks[] = $task->subTasks()->create($subtaskData);
        }

        return response()->json([
            'message' => 'Subtasks created successfully',
            'subtasks' => $createdSubtasks
        ], 201);
    }


    /**
     * Display a single subtask.
     */
    public function show( SubTask $subtask)
    {
        
        return response()->json($subtask);
    }

    /**
     * Update a subtask.
     */
    public function update(Request $request, SubTask $subtask)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'boolean',
            'end_date' => 'nullable|date',
        ]);

        $subtask->update($validated);
 
        return response()->json($subtask);
    }

    /**
     * Remove a subtask.
     */
    public function destroy(SubTask $subtask)
    {
        $subtask->delete();
        return response()->json(['message' => 'Subtask deleted successfully']);
    }
}
