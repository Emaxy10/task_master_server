<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Mail\TaskReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;



class SendTaskReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-task-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
         $now = Carbon::now()->format('Y-m-d');

        $tasks = Task::whereDate('start_date', $now)
            ->orWhereDate('due_date', $now)
            ->where('is_completed', false)
            ->with('user') // Ensure user relation is loaded
            ->get();

        foreach ($tasks as $task) {
            try{
                 Mail::to($task->user)->send(new TaskReminderMail($task));
            }catch(\Exception $e){
                $this->error($e->getMessage());
            }
        }

        $this->info("Sent " . $tasks->count() . " task reminders.");

    }
}
