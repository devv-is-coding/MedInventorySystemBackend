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
        Schema::create('monthly_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained('medicines')->onDelete('cascade');
            $table->smallInteger('year');
            $table->smallInteger('month');
            $table->integer('opening_stock')->default(0);
            $table->integer('total_return')->default(0);
            $table->integer('total_donation')->default(0);
            $table->integer('total_new_added')->default(0);
            $table->integer('total_dispensed')->default(0);
            $table->integer('closing_stock')->default(0);
            $table->timestamps();

            $table->unique(['year', 'month', 'medicine_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_summaries');
    }
};
