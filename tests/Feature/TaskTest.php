<?php

use App\Models\Matter;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a task can be created against a matter', function () {
    $user = User::factory()->create();
    $matter = Matter::factory()->create();

    $this->actingAs($user)->post(route('matters.tasks.store', $matter), [
        'title' => 'Follow up with client',
        'priority' => 'medium',
    ]);

    $this->assertDatabaseHas('tasks', [
        'matter_id' => $matter->id,
        'title' => 'Follow up with client',
        'status' => 'open',
    ]);
});

test('marking a task done stamps completed_at and logs activity', function () {
    $user = User::factory()->create();
    $matter = Matter::factory()->create();
    $task = Task::factory()->create(['matter_id' => $matter->id, 'status' => 'open']);

    $this->actingAs($user)->patch(route('tasks.update', $task), [
        'status' => 'done',
        'priority' => $task->priority,
        'due_date' => $task->due_date,
    ]);

    $task->refresh();

    expect($task->status)->toBe('done')
        ->and($task->completed_at)->not->toBeNull();

    $this->assertDatabaseHas('activity_logs', [
        'matter_id' => $matter->id,
        'action' => 'task_status_changed',
    ]);
});

test('a task created with a source_document_id records the link', function () {
    $user = User::factory()->create();
    $matter = Matter::factory()->create();
    $document = \App\Models\Document::factory()->create(['matter_id' => $matter->id]);

    $this->actingAs($user)->post(route('matters.tasks.store', $matter), [
        'title' => 'Renewal notice deadline',
        'priority' => 'high',
        'due_date' => now()->addDays(30)->format('Y-m-d'),
        'source_document_id' => $document->id,
    ]);

    $this->assertDatabaseHas('tasks', [
        'matter_id' => $matter->id,
        'source_document_id' => $document->id,
    ]);
});
