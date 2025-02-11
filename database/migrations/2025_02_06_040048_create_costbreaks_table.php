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
        Schema::create('costbreaks', function (Blueprint $table) {
            $table->id();
            $table->decimal('airfare', 10, 2);
            $table->decimal('subsistence', 10, 2);
            $table->decimal('incidental', 10, 2);
            $table->decimal('registration', 10, 2);
            $table->decimal('visa', 10, 2);
            $table->decimal('insurance', 10, 2);
            $table->decimal('warm_clothes', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('training_id'); // Change from foreignId() to string()
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costbreaks');
    }
};
