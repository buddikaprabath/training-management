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
        Schema::create('training_institutes', function (Blueprint $table) {
            $table->string('training_id'); 
            $table->unsignedBigInteger('institute_id');
            $table->timestamps();

            // Define composite primary key
            $table->primary(['training_id', 'institute_id']);

            // Foreign key constraints
            $table->foreign('training_id')->references('id')->on('trainings')->onDelete('cascade');
            $table->foreign('institute_id')->references('id')->on('institutes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_institutes');
    }
};
