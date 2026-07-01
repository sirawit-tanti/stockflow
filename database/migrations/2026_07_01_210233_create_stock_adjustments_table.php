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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number')->unique();

            $table->foreignId('warehouse_id')
                ->constrained('warehouses')
                ->restrictOnDelete();

            $table->string('adjustment_type');

            $table->foreignId('adjusted_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->dateTime('adjusted_at');
            $table->text('reason');
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
