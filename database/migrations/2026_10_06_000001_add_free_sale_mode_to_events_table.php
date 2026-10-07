<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->enum('sale_mode', [
                'general_admission',
                'assigned_seat',
                'free_sale',
            ])->default('general_admission')->change();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->enum('sale_mode', [
                'general_admission',
                'assigned_seat',
            ])->default('general_admission')->change();
        });
    }
};
