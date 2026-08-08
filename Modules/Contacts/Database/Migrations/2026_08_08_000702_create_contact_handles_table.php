<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_handles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_id')->index();
            $table->string('platform');
            $table->string('handle')->nullable();
            $table->string('platform_uid')->nullable();
            $table->string('profile_url')->nullable();
            $table->timestamps();

            $table->unique(['contact_id', 'platform']);
            $table->foreign('contact_id')->references('id')->on('contacts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_handles');
    }
};
