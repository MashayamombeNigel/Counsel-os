<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): Response
    {
        $tasks = Task::all();

        return view('task.index', [
            'tasks' => $tasks,
        ]);
    }

    public function create(Request $request): Response
    {
        return view('task.create');
    }

    public function store(TaskStoreRequest $request): Response
    {
        $task = Task::create($request->validated());

        $request->session()->flash('task.id', $task->id);

        return redirect()->route('tasks.index');
    }

    public function show(Request $request, Task $task): Response
    {
        return view('task.show', [
            'task' => $task,
        ]);
    }

    public function edit(Request $request, Task $task): Response
    {
        return view('task.edit', [
            'task' => $task,
        ]);
    }

    public function update(TaskUpdateRequest $request, Task $task): Response
    {
        $task->update($request->validated());

        $request->session()->flash('task.id', $task->id);

        return redirect()->route('tasks.index');
    }

    public function destroy(Request $request, Task $task): Response
    {
        $task->delete();

        return redirect()->route('tasks.index');
    }
}
