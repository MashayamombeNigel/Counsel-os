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

        Schema::create('research_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matter_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->text('query');
            $table->longText('response')->nullable();
            $table->json('sources_json')->nullable();
            $table->string('model_name')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_sessions');
    }
};
