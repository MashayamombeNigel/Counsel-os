<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('document_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained();
            $table->text('summary')->nullable();
            $table->json('key_parties_json')->nullable();
            $table->json('key_clauses_json')->nullable();
            $table->json('risks_json')->nullable();
            $table->json('obligations_json')->nullable();
            $table->json('deadlines_json')->nullable();
            $table->json('questions_json')->nullable();
            $table->string('model_name')->nullable();
            $table->longText('raw_ai_response')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_insights');
    }
};
