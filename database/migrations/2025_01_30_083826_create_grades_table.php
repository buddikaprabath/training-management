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
        Schema::create('grades', function (Blueprint $table) {
            $table->string('unique_identifier');
            $table->unsignedBigInteger('participant_id');
            $table->unsignedBigInteger('subject_id');
            $table->string('grade'); // Normal attribute for storing grade
            $table->timestamps();

            // Define composite primary key
            $table->primary(['unique_identifier', 'participant_id', 'subject_id']);

            // Foreign key constraints
            $table->foreign('participant_id')->references('id')->on('participants')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
