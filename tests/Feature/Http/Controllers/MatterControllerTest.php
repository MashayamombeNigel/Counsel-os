<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\Matter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\MatterController
 */
final class MatterControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $matters = Matter::factory()->count(3)->create();

        $response = $this->get(route('matters.index'));

        $response->assertOk();
        $response->assertViewIs('matter.index');
        $response->assertViewHas('matters', $matters);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('matters.create'));

        $response->assertOk();
        $response->assertViewIs('matter.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\MatterController::class,
            'store',
            \App\Http\Requests\MatterStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $client = Client::factory()->create();
        $title = fake()->sentence(4);
        $status = fake()->randomElement(/** enum_attributes **/);

        $response = $this->post(route('matters.store'), [
            'client_id' => $client->id,
            'title' => $title,
            'status' => $status,
        ]);

        $matters = Matter::query()
            ->where('client_id', $client->id)
            ->where('title', $title)
            ->where('status', $status)
            ->get();
        $this->assertCount(1, $matters);
        $matter = $matters->first();

        $response->assertRedirect(route('matters.index'));
        $response->assertSessionHas('matter.id', $matter->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $matter = Matter::factory()->create();

        $response = $this->get(route('matters.show', $matter));

        $response->assertOk();
        $response->assertViewIs('matter.show');
        $response->assertViewHas('matter', $matter);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $matter = Matter::factory()->create();

        $response = $this->get(route('matters.edit', $matter));

        $response->assertOk();
        $response->assertViewIs('matter.edit');
        $response->assertViewHas('matter', $matter);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\MatterController::class,
            'update',
            \App\Http\Requests\MatterUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $matter = Matter::factory()->create();
        $client = Client::factory()->create();
        $title = fake()->sentence(4);
        $status = fake()->randomElement(/** enum_attributes **/);

        $response = $this->put(route('matters.update', $matter), [
            'client_id' => $client->id,
            'title' => $title,
            'status' => $status,
        ]);

        $matter->refresh();

        $response->assertRedirect(route('matters.index'));
        $response->assertSessionHas('matter.id', $matter->id);

        $this->assertEquals($client->id, $matter->client_id);
        $this->assertEquals($title, $matter->title);
        $this->assertEquals($status, $matter->status);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $matter = Matter::factory()->create();

        $response = $this->delete(route('matters.destroy', $matter));

        $response->assertRedirect(route('matters.index'));

        $this->assertModelMissing($matter);
    }
}
