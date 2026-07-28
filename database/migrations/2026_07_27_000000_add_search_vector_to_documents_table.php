<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Generated column — Postgres keeps this in sync automatically whenever
        // original_name or extracted_text changes. No model observer needed.
        DB::statement(<<<SQL
            ALTER TABLE documents
            ADD COLUMN search_vector tsvector
            GENERATED ALWAYS AS (
                to_tsvector('english', coalesce(original_name, '') || ' ' || coalesce(extracted_text, ''))
            ) STORED
        SQL);

        DB::statement('CREATE INDEX documents_search_vector_idx ON documents USING GIN (search_vector)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS documents_search_vector_idx');
        DB::statement('ALTER TABLE documents DROP COLUMN IF EXISTS search_vector');
    }
};
