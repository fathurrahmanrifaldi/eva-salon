<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20)->nullable()->unique(); // format +62, NULL diizinkan berkali-kali
            $table->string('email')->nullable();
            $table->timestamp('consent_at')->nullable();      // persetujuan UU PDP
            $table->timestamp('last_visit_at')->nullable();   // dasar tinjauan retensi 24 bulan
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
