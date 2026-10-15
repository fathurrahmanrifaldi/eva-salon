<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->restrictOnDelete();
            $table->integer('amount'); // boleh negatif untuk koreksi
            $table->enum('method', ['cash', 'transfer', 'qris']);
            $table->enum('kind', ['payment', 'correction'])->default('payment');
            $table->dateTime('paid_at');
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->string('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
