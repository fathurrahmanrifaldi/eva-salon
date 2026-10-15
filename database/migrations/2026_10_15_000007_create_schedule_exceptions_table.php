<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->nullable()->constrained('staff')->restrictOnDelete();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->enum('type', ['salon_closed', 'staff_leave']);
            $table->string('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['starts_on', 'ends_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_exceptions');
    }
};
