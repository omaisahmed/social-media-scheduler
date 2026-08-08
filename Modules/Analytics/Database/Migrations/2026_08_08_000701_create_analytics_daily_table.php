<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_daily', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id')->index();
            $table->unsignedBigInteger('account_id')->nullable()->index();
            $table->string('platform')->index();
            $table->date('date')->index();
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('reach')->default(0);
            $table->unsignedBigInteger('engagements')->default(0);
            $table->integer('followers_delta')->default(0);
            $table->json('raw')->nullable();
            $table->timestamps();

            $table->unique(['business_id', 'account_id', 'platform', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_daily');
    }
};
