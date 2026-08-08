<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('source')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('post_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->index();
            $table->unsignedBigInteger('social_account_id')->index();
            $table->string('platform');
            $table->string('status')->default('pending');
            $table->string('external_id')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['post_id', 'social_account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_accounts');
        Schema::dropIfExists('posts');
    }
};
