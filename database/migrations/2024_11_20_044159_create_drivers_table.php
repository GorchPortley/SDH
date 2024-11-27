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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('brand');
            $table->string('model');
            $table->json('card_image');
            $table->string('tag')->nullable();
            $table->boolean('active')->default(false);
            $table->enum('category',
                ['Subwoofer', 'Woofer', 'Tweeter', 'Compression Driver', 'Exciter', 'Other']);
            $table->integer('size');
            $table->string('impedance');
            $table->integer('power');
            $table->decimal('price', 8, 2)->default(0)->nullable();
            $table->string('link')->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->json('factory_specs')->nullable();
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
        Schema::dropIfExists('drivers');
    }
};
