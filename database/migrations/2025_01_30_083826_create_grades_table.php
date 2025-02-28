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
            $table->string('training_id');
            $table->unsignedBigInteger('participant_id');
            $table->unsignedBigInteger('subject_id');
            $table->string('grade'); // Normal attribute for storing grade
            $table->timestamps();

            // Define composite primary key
            $table->primary(['training_id', 'participant_id', 'subject_id']);

            // Foreign key constraints
            $table->foreign('training_id')->references('id')->on('trainings')->onDelete('cascade');
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
