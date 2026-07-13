<?php

use App\Models\Matter;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a logged in user can upload a valid pdf to a matter', function () {
    $user = User::factory()->create();
    $matter = Matter::factory()->create();

    $file = UploadedFile::fake()->create('lease.pdf', 500, 'application/pdf');

    $response = $this->actingAs($user)->post(route('matters.documents.store', $matter), [
        'file' => $file,
        'document_type' => 'lease',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('documents', [
        'matter_id' => $matter->id,
        'original_name' => 'lease.pdf',
        'processing_status' => 'uploaded',
    ]);
});

test('upload rejects a file type that is not pdf or docx', function () {
    $user = User::factory()->create();
    $matter = Matter::factory()->create();

    $file = UploadedFile::fake()->create('photo.jpg', 500, 'image/jpeg');

    $response = $this->actingAs($user)->post(route('matters.documents.store', $matter), [
        'file' => $file,
        'document_type' => 'other',
    ]);

    $response->assertSessionHasErrors('file');
    $this->assertDatabaseCount('documents', 0);
});

test('upload rejects a file over the 20MB cap', function () {
    $user = User::factory()->create();
    $matter = Matter::factory()->create();

    // size is in kilobytes - 20481 KB is 1KB over the 20MB (20480 KB) cap
    $file = UploadedFile::fake()->create('huge.pdf', 20481, 'application/pdf');

    $response = $this->actingAs($user)->post(route('matters.documents.store', $matter), [
        'file' => $file,
        'document_type' => 'other',
    ]);

    $response->assertSessionHasErrors('file');
    $this->assertDatabaseCount('documents', 0);
});

test('upload rejects an invalid document_type', function () {
    $user = User::factory()->create();
    $matter = Matter::factory()->create();

    $file = UploadedFile::fake()->create('lease.pdf', 500, 'application/pdf');

    $response = $this->actingAs($user)->post(route('matters.documents.store', $matter), [
        'file' => $file,
        'document_type' => 'not_a_real_type',
    ]);

    $response->assertSessionHasErrors('document_type');
});

test('guests cannot upload documents', function () {
    $matter = Matter::factory()->create();
    $file = UploadedFile::fake()->create('lease.pdf', 500, 'application/pdf');

    $response = $this->post(route('matters.documents.store', $matter), [
        'file' => $file,
        'document_type' => 'lease',
    ]);

    $response->assertRedirect(route('login'));
});
