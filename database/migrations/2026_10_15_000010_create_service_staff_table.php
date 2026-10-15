<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_staff', function (Blueprint $table) {
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->foreignId('staff_id')->constrained('staff')->restrictOnDelete();
            $table->primary(['service_id', 'staff_id']); // unik sekaligus indeks
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_staff');
    }
};
