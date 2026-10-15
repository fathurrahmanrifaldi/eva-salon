<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->restrictOnDelete();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();

            // Snapshot saat booking dibuat
            $table->string('service_name');
            $table->unsignedInteger('price_snapshot');
            $table->unsignedSmallInteger('duration_snapshot');
            $table->unsignedSmallInteger('buffer_snapshot');
            $table->unsignedTinyInteger('sequence'); // urutan layanan dalam satu booking

            $table->timestamps();

            $table->unique(['booking_id', 'sequence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_items');
    }
};
