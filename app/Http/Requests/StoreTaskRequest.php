<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
        return [
            //
                'title' => 'required|string|max:255|unique:tasks,title',
                'description'=> 'nullable|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'due_date' => 'nullable|date|after_or_equal:start_date',
                'status'=> 'required|in:pending,in_progress,completed,overdue',
                'is_completed' => 'boolean',
                'completed_at' => 'nullable|date',
                'is_recurring' => 'boolean',
                'recurrence_rule' => 'nullable|string|max:255', // You can customize this with stricter rules if needed
                'custom_date'=> 'nullable|date',
                'custom_time'=> 'nullable',
                'weekly_day' => 'nullable|string'
                
        ];
    }
}
