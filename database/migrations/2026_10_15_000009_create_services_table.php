<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('service_categories')->restrictOnDelete();
            $table->string('name');
            $table->text('description')->nullable();       // paket: satu poin per baris
            $table->unsignedInteger('price');              // Rupiah; harga minimum jika price_max terisi
            $table->unsignedInteger('price_max')->nullable();
            $table->unsignedSmallInteger('duration_minutes');
            $table->unsignedSmallInteger('buffer_minutes')->default(0);
            $table->string('image_path')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_sensitive')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_available', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
