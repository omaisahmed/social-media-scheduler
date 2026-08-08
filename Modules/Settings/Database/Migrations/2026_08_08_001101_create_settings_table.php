<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('model_type')->index();
            $table->unsignedBigInteger('model_id')->index();
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['model_type', 'model_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
