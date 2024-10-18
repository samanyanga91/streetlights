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
        Schema::create('streetlights', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('notes')->nullable();
            $table->text('request_details')->nullable();
            $table->text('description')->nullable();
            $table->geometry('location', 'POINT');
            $table->string('status')->default('working');
            $table->string('energy_source')->default('grid');
            $table->string('crime_level')->default('low');
            $table->float('score')->default('0');
            $table->date('installed_on')->nullable();
            $table->string('ward')->nullable();
            $table->string('land_use')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *         'name',
     */
    public function down(): void
    {
        Schema::dropIfExists('streetlights');
    }
};
