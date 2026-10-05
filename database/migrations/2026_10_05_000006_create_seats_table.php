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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                ->constrained('events')
                ->cascadeOnDelete();

            $table->foreignId('seat_map_id')
                ->constrained('seat_maps')
                ->cascadeOnDelete();

            $table->foreignId('ticket_type_id')
                ->constrained('ticket_types')
                ->cascadeOnDelete();

            $table->string('zone', 50)->nullable();
            $table->string('row_name', 20)->nullable();
            $table->string('seat_number', 20);

            $table->enum('status', [
                'available',
                'held',
                'sold',
            ])->default('available');

            $table->timestamps();

            $table->unique([
                'event_id',
                'seat_number',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
