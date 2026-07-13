<?php

use App\Models\Document;
use App\Models\Matter;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * NOTE: these tests verify that a guest (no logged-in user at all)
 * cannot reach any core route - they do NOT test that one logged-in
 * user is blocked from another user's data. Firm/user-level data
 * isolation doesn't exist in this MVP (see spec Section 4's explicit
 * scope boundary and the Phase 2 plan's RBAC epic, deferred to
 * Phase 3) - writing a test for it here would either fail correctly
 * against a feature that was never built, or quietly pressure scope
 * creep into what's supposed to be a stability-focused testing epic.
 *
 * CSRF protection itself is exercised implicitly: every POST/PUT/PATCH
 * test in this suite goes through Laravel's normal test HTTP client,
 * which submits a valid token automatically. A dedicated "missing
 * token is rejected" test is intentionally omitted here since
 * Laravel's CSRF middleware is framework-level, already covered by
 * Laravel's own test suite, and not something this app customized -
 * re-testing framework defaults adds noise without real signal.
 */

test('guest is redirected from the dashboard', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('guest is redirected from the clients index', function () {
    $this->get(route('clients.index'))->assertRedirect(route('login'));
});

test('guest is redirected from the matters index', function () {
    $this->get(route('matters.index'))->assertRedirect(route('login'));
});

test('guest is redirected from a matter workspace', function () {
    $matter = Matter::factory()->create();
    $this->get(route('matters.show', $matter))->assertRedirect(route('login'));
});

test('guest is redirected from a document viewer', function () {
    $document = Document::factory()->create();
    $this->get(route('documents.show', $document))->assertRedirect(route('login'));
});

test('guest cannot submit the research form', function () {
    $matter = Matter::factory()->create();
    $this->post(route('matters.research.store', $matter), ['query' => 'test'])
        ->assertRedirect(route('login'));
});

test('guest cannot create a client', function () {
    $this->post(route('clients.store'), ['name' => 'Should not save'])
        ->assertRedirect(route('login'));

    $this->assertDatabaseMissing('clients', ['name' => 'Should not save']);
});

test('guest cannot upload a document', function () {
    $matter = Matter::factory()->create();

    $this->post(route('matters.documents.store', $matter), [])
        ->assertRedirect(route('login'));
});
