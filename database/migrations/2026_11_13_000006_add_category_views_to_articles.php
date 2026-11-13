<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Blog : catégorie + compteur de vues sur les articles (Phase 4).
 * Idempotent (hasColumn). En prod :
 *   php artisan migrate --path=database/migrations/2026_11_13_000006_add_category_views_to_articles.php --force
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('articles')) {
            return;
        }

        Schema::table('articles', function (Blueprint $table) {
            if (! Schema::hasColumn('articles', 'category')) {
                $table->string('category')->nullable()->after('slug')->index();
            }
            if (! Schema::hasColumn('articles', 'views')) {
                $table->unsignedInteger('views')->default(0)->after('category');
            }
            if (! Schema::hasColumn('articles', 'author')) {
                $table->string('author')->nullable()->after('views');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('articles')) {
            return;
        }

        Schema::table('articles', function (Blueprint $table) {
            foreach (['author', 'views', 'category'] as $col) {
                if (Schema::hasColumn('articles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
