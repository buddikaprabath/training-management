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
        Schema::create('sureties', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('epf_number')->nullable();
            $table->text('address')->nullable();
            $table->string('mobile')->nullable();
            $table->string('nic')->nullable();
            $table->decimal('salary_scale', 10, 2)->nullable();
            $table->string('designation')->nullable();
            $table->foreignId('participant_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sureties');
    }
};
