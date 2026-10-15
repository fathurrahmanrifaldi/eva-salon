<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salon_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('address');
            $table->string('phone', 20)->nullable();
            $table->string('wa_number', 20); // format +62, sumber tombol wa.me
            $table->text('map_embed')->nullable();
            $table->string('operating_note')->nullable();

            // Aturan bisnis yang bisa diatur owner
            $table->unsignedSmallInteger('cancel_limit_minutes')->default(30);
            $table->unsignedSmallInteger('hold_default_minutes')->default(30);
            $table->unsignedSmallInteger('hold_max_minutes')->default(120);
            $table->unsignedSmallInteger('pending_alert_hours')->default(24);
            $table->boolean('require_payment_to_complete')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salon_profiles');
    }
};
