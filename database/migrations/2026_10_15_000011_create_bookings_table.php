<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->char('booking_code', 8)->unique(); // acak, tanpa 0/O/1/I

            $table->foreignId('customer_id')->nullable()->constrained('customers')->restrictOnDelete();
            $table->string('customer_name');           // snapshot, wajib
            $table->string('customer_phone', 20)->nullable();

            $table->foreignId('staff_id')->constrained('staff')->restrictOnDelete();

            $table->dateTime('start_at');              // UTC
            $table->dateTime('end_at');                // selesai layanan
            $table->dateTime('blocked_until');         // end_at + buffer, dipakai untuk overlap

            $table->enum('status', ['hold', 'pending', 'confirmed', 'completed', 'cancelled', 'no_show'])
                ->default('pending');
            $table->enum('source', ['online', 'walk_in', 'phone_hold']);

            $table->dateTime('hold_until')->nullable(); // hanya untuk status hold

            $table->unsignedInteger('total_price');
            $table->unsignedSmallInteger('total_duration');
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('consent_at')->nullable(); // dicatat dari form online
            $table->timestamps();

            // Indeks untuk pengecekan bentrok dan tampilan kalender
            $table->index(['staff_id', 'start_at']);
            $table->index(['staff_id', 'blocked_until']);
            $table->index(['status', 'start_at']);
            $table->index('customer_id');
            $table->index('hold_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
