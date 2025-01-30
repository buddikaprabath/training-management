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
        Schema::create('participants', function (Blueprint $table) {
            $table->string('id')->primary();  // Primary key with custom format
            $table->string('name');
            $table->string('epf_number');
            $table->string('designation');
            $table->decimal('salary_scale', 10, 2);
            $table->string('location');
            $table->string('obligatory_period');
            $table->decimal('cost_per_head');
            $table->date('bond_completion_date');
            $table->decimal('bond_value', 10, 2);
            $table->date('date_of_signing');
            $table->decimal('age_as_at_commencement_date', 2, 2, 2);
            $table->date('date_of_appointment');
            $table->date('date_of_appointment_to_the_present');
            $table->string('post');
            $table->date('date_of_birth');
            $table->foreignId('division_id')->constrained()->onDelete('cascade');
            $table->foreignId('training_id')->constrained()->onDelete('cascade');
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
