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
            if (! Schema::hasColumn('media_assets', 'thumb_path')) {
                $table->string('thumb_path')->nullable()->after('path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('media_assets', function (Blueprint $table) {
            if (Schema::hasColumn('media_assets', 'thumb_path')) {
                $table->dropColumn('thumb_path');
            }
        });
    }
};
