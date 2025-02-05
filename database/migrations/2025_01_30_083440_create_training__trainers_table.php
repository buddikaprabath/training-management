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
        Schema::create('training_trainers', function (Blueprint $table) {
            $table->string('training_id');
            $table->unsignedBigInteger('trainer_id');
            $table->timestamps();

            // Define composite primary key
            $table->primary(['training_id', 'trainer_id']);

            // Foreign key constraints
            $table->foreign('training_id')->references('id')->on('trainings')->onDelete('cascade');
            $table->foreign('trainer_id')->references('id')->on('trainers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_trainers');
    }
};
