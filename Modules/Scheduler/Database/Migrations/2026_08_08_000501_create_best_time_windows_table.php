<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('best_time_windows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id')->index();
            $table->string('platform');
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('score')->default(0);
            $table->timestamps();

            $table->unique(['business_id', 'platform', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('best_time_windows');
    }
};
