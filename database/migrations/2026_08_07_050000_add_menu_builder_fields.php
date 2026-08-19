<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            if (!Schema::hasColumn('menus', 'position')) {
                $table->string('position')->default('navbar')->after('target');
            }
            if (!Schema::hasColumn('menus', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('pdf_file');
            }
            if (!Schema::hasColumn('pages', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title');
            }
            if (!Schema::hasColumn('pages', 'status')) {
                $table->string('status')->default('publish')->after('seo_description');
            }
            if (!Schema::hasColumn('pages', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['position', 'deleted_at']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'seo_description', 'status', 'deleted_at']);
        });
    }
};
