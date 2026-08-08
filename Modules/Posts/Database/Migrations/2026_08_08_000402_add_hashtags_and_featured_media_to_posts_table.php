<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'hashtags')) {
                $table->string('hashtags')->nullable()->after('content');
            }

            if (! Schema::hasColumn('posts', 'featured_media_id')) {
                $table->unsignedBigInteger('featured_media_id')->nullable()->after('source_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'hashtags')) {
                $table->dropColumn('hashtags');
            }

            if (Schema::hasColumn('posts', 'featured_media_id')) {
                $table->dropColumn('featured_media_id');
            }
        });
    }
};
