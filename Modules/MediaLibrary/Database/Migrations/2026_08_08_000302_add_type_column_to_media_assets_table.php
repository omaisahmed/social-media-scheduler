<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media_assets', function (Blueprint $table) {
            if (Schema::hasColumn('media_assets', 'name') && ! Schema::hasColumn('media_assets', 'original_name')) {
                $table->renameColumn('name', 'original_name');
            }

            if (Schema::hasColumn('media_assets', 'meta') && ! Schema::hasColumn('media_assets', 'metadata')) {
                $table->renameColumn('meta', 'metadata');
            }

            if (! Schema::hasColumn('media_assets', 'type')) {
                $table->string('type')->index()->after('height');
            }

            if (Schema::hasColumn('media_assets', 'filename')) {
                $table->dropColumn('filename');
            }

            if (Schema::hasColumn('media_assets', 'alt_text')) {
                $table->dropColumn('alt_text');
            }
        });
    }

    public function down(): void
    {
        Schema::table('media_assets', function (Blueprint $table) {
            if (Schema::hasColumn('media_assets', 'type')) {
                $table->dropColumn('type');
            }

            if (Schema::hasColumn('media_assets', 'original_name') && ! Schema::hasColumn('media_assets', 'name')) {
                $table->renameColumn('original_name', 'name');
            }

            if (Schema::hasColumn('media_assets', 'metadata') && ! Schema::hasColumn('media_assets', 'meta')) {
                $table->renameColumn('metadata', 'meta');
            }

            if (! Schema::hasColumn('media_assets', 'filename')) {
                $table->string('filename')->nullable()->after('path');
            }

            if (! Schema::hasColumn('media_assets', 'alt_text')) {
                $table->string('alt_text')->nullable()->after('height');
            }
        });
    }
};
