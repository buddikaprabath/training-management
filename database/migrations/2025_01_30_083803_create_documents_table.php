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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('status')->nullable();
            $table->date('date_of_submitting')->nullable();
            $table->string('training_id');
            $table->string('participant_id')->nullable();
            $table->string('file_path')->nullable(); // New column for storing the document file path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
