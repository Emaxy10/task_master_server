<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $taskId = $this->route('task')->id ?? null;
         return [
            'title' => [
            'required',
            'string',
            'max:255',
            Rule::unique('tasks')
                    ->where(fn($query) => $query->where('user_id', Auth::id()))
                    ->ignore($taskId),
            ],
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'sometimes|required|in:pending,in_progress,completed,overdue',
            'priority' => 'sometimes|required|in:low,medium,high',
            'is_completed' => 'boolean',
            'completed_at' => 'nullable|date',
            'is_recurring' => 'boolean',
            'recurrence_rule' => 'nullable|string|max:255',
            'weekly_day' => 'nullable|string'
            
        ];
    }
}
