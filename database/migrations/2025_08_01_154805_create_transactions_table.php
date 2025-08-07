<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->string('receive_from');
            $table->string('patient_name');
            $table->string('optometrist_name');
            $table->string('pay_for');
            $table->string('frame_type');
            $table->decimal('frame_price', 10, 2);
            $table->string('lens_type');
            $table->decimal('lens_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->string('amount_in_words');
            $table->string('date')->default(now()->format('Y-m-d')); // Default to current date
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
