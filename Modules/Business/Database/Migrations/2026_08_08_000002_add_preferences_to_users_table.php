<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'business_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('business_id')->nullable()->index();
                $table->string('timezone')->default('UTC');
                $table->string('locale')->default('en');
                $table->string('theme')->default('system');
                $table->string('avatar_path')->nullable();
                $table->boolean('is_active')->default(true);
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['business_id', 'timezone', 'locale', 'theme', 'avatar_path', 'is_active']);
        });
    }
};
