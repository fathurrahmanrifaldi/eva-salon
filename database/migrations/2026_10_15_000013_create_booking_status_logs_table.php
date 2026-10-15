<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const STATUSES = ['hold', 'pending', 'confirmed', 'completed', 'cancelled', 'no_show'];

    public function up(): void
    {
        Schema::create('booking_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->restrictOnDelete();
            $table->enum('from_status', self::STATUSES)->nullable();
            $table->enum('to_status', self::STATUSES);
            $table->enum('actor_type', ['customer', 'kasir', 'owner', 'system']);
            $table->foreignId('changed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->string('reason')->nullable(); // wajib untuk cancel dan no_show di level aplikasi
            $table->timestamp('created_at')->useCurrent(); // append-only

            $table->index(['booking_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_status_logs');
    }
};
