<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position')->nullable(); // kapster, terapis, dll.
            $table->enum('gender', ['female', 'male'])->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('is_active')->default(true); // nonaktif, tidak dihapus
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
