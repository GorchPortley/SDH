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
        Schema::create('designs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('name')->nullable();
            $table->string('tag')->nullable();
            $table->boolean('active')->default(false);
            $table->enum('category',
                ['Subwoofer', 'Full-Range', 'Two-Way', 'Three-Way','Four-Way+','Portable', 'Esoteric', 'System']);
            $table->decimal('price', 8, 2)->default(0);
            $table->decimal('build_cost', 8, 2)->default(0)->nullable();
            $table->integer('impedance')->default(4);
            $table->integer('power')->nullable();
            $table->text('summary');
            $table->longText('description')->nullable();
            $table->json('bill_of_materials')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designs');
    }
};
