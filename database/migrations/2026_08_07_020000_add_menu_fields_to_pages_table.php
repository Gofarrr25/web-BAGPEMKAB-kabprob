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
        Schema::table('pages', function (Blueprint $table) {
            $table->string('menu_group')->nullable()->after('slug')->comment('Kelompok Menu Utama (Profil, Layanan, Dokumen, Informasi, Hubungi, dll)');
            $table->foreignId('parent_id')->nullable()->after('menu_group')->constrained('pages')->onDelete('set null');
            $table->integer('order_index')->default(0)->after('parent_id');
            $table->boolean('is_in_menu')->default(true)->after('order_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['menu_group', 'parent_id', 'order_index', 'is_in_menu']);
        });
    }
};
