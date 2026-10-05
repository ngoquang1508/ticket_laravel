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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('location_id')
                ->constrained('locations')
                ->cascadeOnDelete();

            $table->foreignId('parent_event_id')
                ->nullable()
                ->constrained('events')
                ->nullOnDelete();

            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('image', 500)->nullable();

            $table->dateTime('starts_at');
            $table->dateTime('ends_at');

            $table->enum('sale_mode', [
                'general_admission',
                'assigned_seat',
            ])->default('general_admission');

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
