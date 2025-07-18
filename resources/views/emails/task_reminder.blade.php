<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
</head>
<body>
  <h2>Task Reminder</h2>
  <p>Hello {{ $task->user->name ?? 'User' }},</p>
  <p>This is a reminder for your task:</p>

  <ul>
    <li><strong>Title:</strong> {{ $task->title }}</li>
    <li><strong>Description:</strong> {{ $task->description ?? 'N/A' }}</li>
    <li><strong>Due Date:</strong> {{ $task->due_date ?? 'N/A' }}</li>
    <li><strong>Status:</strong> {{ ucfirst($task->status) }}</li>
  </ul>

  <p>Stay productive!</p>
</body>
</html>
