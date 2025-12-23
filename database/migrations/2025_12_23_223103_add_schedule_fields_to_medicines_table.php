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
        Schema::table('medicines', function (Blueprint $table) {
            $table->string('schedule_type')->default('hours');
            $table->unsignedInteger('frequency_days')->default(0);
            $table->json('weekdays')->nullable();
            $table->json('times')->nullable();
            $table->json('dates')->nullable();
            $table->string('time_of_day')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropColumn([
                'schedule_type',
                'frequency_days',
                'weekdays',
                'times',
                'dates',
                'time_of_day',
            ]);
        });
    }
};
