<?php

namespace App\Http\Controllers;

use App\Models\Matter;
use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $tasks,
    ) {}

    /**
     * Route: POST /matters/{matter}/tasks
     * Handles both manual task creation and "convert deadline to
     * task" - the latter arrives here with source_document_id
     * already set from the prefilled form in the AI Insights tab.
     */
    public function store(StoreTaskRequest $request, Matter $matter): RedirectResponse
    {
        $this->tasks->create($matter, $request->validated(), $request->user()->id);

        return redirect()
            ->route('matters.show', ['matter' => $matter, 'tab' => 'tasks'])
            ->with('status', 'Task created.');
    }

    /**
     * Route: PATCH /tasks/{task}
     * Status/priority/due_date changes - status transitions write
     * a timeline entry via TaskService::update().
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $this->tasks->update($task, $request->validated());

        return redirect()
            ->route('matters.show', ['matter' => $task->matter, 'tab' => 'tasks'])
            ->with('status', 'Task updated.');
    }
}
