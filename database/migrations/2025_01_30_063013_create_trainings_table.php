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
        Schema::create('trainings', function (Blueprint $table) {
            $table->string('id')->primary();  // Primary key with custom format
            $table->string('training_code')->unique();  // Training code with custom format
            $table->string('training_name');
            $table->string('mode_of_delivery');
            $table->date('training_period_from');
            $table->date('training_period_to');
            $table->integer('total_training_hours');
            $table->decimal('total_program_cost', 10, 2);
            $table->string('country')->nullable();
            $table->string('training_structure')->nullable();
            $table->date('exp_date')->nullable();
            $table->integer('batch_size')->nullable();
            $table->text('training_custodian')->nullable();
            $table->string('course_type');
            $table->string('category');
            $table->date('dead_line');
            $table->boolean('training_status')->nullable();
            $table->boolean('feedback_form')->nullable();
            $table->boolean('e_report')->nullable();
            $table->boolean('warm_clothe_allowance')->nullable();
            $table->boolean('presentation')->nullable();
            $table->foreignId('division_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('section_id')->nullable()->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
