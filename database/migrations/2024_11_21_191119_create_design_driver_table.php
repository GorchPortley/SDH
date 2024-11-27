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
        Schema::create('design_driver', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_id');
            $table->foreignId('driver_id');
            $table->enum('position', ['LF','LMF','MF','HMF','HF','Other']);
            $table->integer('quantity');
            $table->decimal('low_frequency', 10, 2)->nullable();
            $table->decimal('high_frequency', 10, 2)->nullable();
            $table->decimal('air_volume', 6, 2)->nullable();
            $table->longtext('description')->nullable();
            $table->json('specifications')->nullable();
            $table->json('frequency_files')->nullable();
            $table->json('impedance_files')->nullable();
            $table->json('other_files')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_driver');
    }
};
