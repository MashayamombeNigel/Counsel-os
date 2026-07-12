<?php

namespace App\Services;

use App\Models\Matter;
use App\Models\Task;

class TaskService
{
    public function __construct(
        protected TimelineService $timeline,
    ) {}

    public function create(Matter $matter, array $data, int $createdBy): Task
    {
        $task = Task::create([
            ...$data,
            'matter_id' => $matter->id,
            'created_by' => $createdBy,
            'status' => 'open',
        ]);

        $this->timeline->recordMatterEvent(
            matter: $matter,
            action: 'task_created',
            description: "Task \"{$task->title}\" created" . ($task->source_document_id ? ' from AI-extracted deadline.' : '.'),
        );

        return $task;
    }

    public function update(Task $task, array $data): Task
    {
        $previousStatus = $task->status;

        $task->update($data);

        if ($previousStatus !== $task->status) {
            if ($task->status === 'done' && ! $task->completed_at) {
                $task->update(['completed_at' => now()]);
            }

            $this->timeline->recordMatterEvent(
                matter: $task->matter,
                action: 'task_status_changed',
                description: "Task \"{$task->title}\" changed from {$previousStatus} to {$task->status}.",
            );
        }

        return $task;
    }
}
