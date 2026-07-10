<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\CreatedBy;
use App\Models\Matter;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\TaskController
 */
final class TaskControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $tasks = Task::factory()->count(3)->create();

        $response = $this->get(route('tasks.index'));

        $response->assertOk();
        $response->assertViewIs('task.index');
        $response->assertViewHas('tasks', $tasks);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('tasks.create'));

        $response->assertOk();
        $response->assertViewIs('task.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\TaskController::class,
            'store',
            \App\Http\Requests\TaskStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $matter = Matter::factory()->create();
        $title = fake()->sentence(4);
        $status = fake()->randomElement(/** enum_attributes **/);
        $priority = fake()->randomElement(/** enum_attributes **/);
        $created_by = CreatedBy::factory()->create();

        $response = $this->post(route('tasks.store'), [
            'matter_id' => $matter->id,
            'title' => $title,
            'status' => $status,
            'priority' => $priority,
            'created_by' => $created_by->id,
        ]);

        $tasks = Task::query()
            ->where('matter_id', $matter->id)
            ->where('title', $title)
            ->where('status', $status)
            ->where('priority', $priority)
            ->where('created_by', $created_by->id)
            ->get();
        $this->assertCount(1, $tasks);
        $task = $tasks->first();

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('task.id', $task->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $task = Task::factory()->create();

        $response = $this->get(route('tasks.show', $task));

        $response->assertOk();
        $response->assertViewIs('task.show');
        $response->assertViewHas('task', $task);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $task = Task::factory()->create();

        $response = $this->get(route('tasks.edit', $task));

        $response->assertOk();
        $response->assertViewIs('task.edit');
        $response->assertViewHas('task', $task);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\TaskController::class,
            'update',
            \App\Http\Requests\TaskUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $task = Task::factory()->create();
        $matter = Matter::factory()->create();
        $title = fake()->sentence(4);
        $status = fake()->randomElement(/** enum_attributes **/);
        $priority = fake()->randomElement(/** enum_attributes **/);
        $created_by = CreatedBy::factory()->create();

        $response = $this->put(route('tasks.update', $task), [
            'matter_id' => $matter->id,
            'title' => $title,
            'status' => $status,
            'priority' => $priority,
            'created_by' => $created_by->id,
        ]);

        $task->refresh();

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('task.id', $task->id);

        $this->assertEquals($matter->id, $task->matter_id);
        $this->assertEquals($title, $task->title);
        $this->assertEquals($status, $task->status);
        $this->assertEquals($priority, $task->priority);
        $this->assertEquals($created_by->id, $task->created_by);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $task = Task::factory()->create();

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));

        $this->assertModelMissing($task);
    }
}
