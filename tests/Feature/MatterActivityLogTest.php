<?php

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Matter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('creating a matter logs a matter_created activity entry', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();

    $this->actingAs($user)->post(route('matters.store'), [
        'client_id' => $client->id,
        'title' => 'Test Matter',
        'status' => 'open',
    ]);

    $matter = Matter::where('title', 'Test Matter')->first();

    $this->assertDatabaseHas('activity_logs', [
        'matter_id' => $matter->id,
        'action' => 'matter_created',
    ]);
});

test('changing matter status logs a status_changed activity entry', function () {
    $user = User::factory()->create();
    $matter = Matter::factory()->create(['status' => 'open']);

    $this->actingAs($user)->put(route('matters.update', $matter), [
        'title' => $matter->title,
        'status' => 'in_review',
    ]);

    $matter->refresh();

    expect($matter->status)->toBe('in_review');
    $this->assertDatabaseHas('activity_logs', [
        'matter_id' => $matter->id,
        'action' => 'status_changed',
    ]);
});

test('moving a matter to closed stamps closed_at', function () {
    $user = User::factory()->create();
    $matter = Matter::factory()->create(['status' => 'open', 'closed_at' => null]);

    $this->actingAs($user)->put(route('matters.update', $matter), [
        'title' => $matter->title,
        'status' => 'closed',
    ]);

    $matter->refresh();

    expect($matter->closed_at)->not->toBeNull();
});

test('updating a matter without changing status does not log a status_changed entry', function () {
    $user = User::factory()->create();
    $matter = Matter::factory()->create(['status' => 'open']);

    $this->actingAs($user)->put(route('matters.update', $matter), [
        'title' => 'Updated title only',
        'status' => 'open',
    ]);

    $this->assertDatabaseMissing('activity_logs', [
        'matter_id' => $matter->id,
        'action' => 'status_changed',
    ]);
});
