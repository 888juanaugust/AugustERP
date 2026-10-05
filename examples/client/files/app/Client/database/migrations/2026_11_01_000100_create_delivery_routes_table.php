<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** The client's delivery routes. A client migration is dated after the template's it builds on. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('area', 100)->nullable();
            $table->foreignId('driver_id')->nullable()->constrained('employees')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_routes');
    }
};
