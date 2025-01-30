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
        Schema::create('cost_break_downs', function (Blueprint $table) {
            $table->id();
            $table->decimal('airfare', 10, 2)->nullable();
            $table->decimal('subsistence_including_travel_day', 10, 2)->nullable();
            $table->decimal('incidental_including_travel_day', 10, 2)->nullable();
            $table->decimal('registration_fee', 10, 2)->nullable();
            $table->decimal('visa_fee', 10, 2)->nullable();
            $table->decimal('travel_insurance', 10, 2)->nullable();
            $table->decimal('warm_clothes', 10, 2)->nullable();
            $table->decimal('total_per_head', 10, 2)->nullable();
            $table->decimal('total_for_all_nomination', 10, 2)->nullable();
            $table->foreignId('training_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_break_downs');
    }
};
