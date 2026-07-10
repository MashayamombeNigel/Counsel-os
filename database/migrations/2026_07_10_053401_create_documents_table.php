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

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matter_id')->constrained();
            $table->foreignId('uploaded_by')->constrained('users', 'id');
            $table->string('filename');
            $table->string('original_name');
            $table->string('storage_path');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->enum('document_type', ["contract","lease","title_deed","correspondence","research","other"]);
            $table->longText('extracted_text')->nullable();
            $table->enum('processing_status', ["uploaded","extracting","analysis_pending","analyzed","failed"])->default('uploaded');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
