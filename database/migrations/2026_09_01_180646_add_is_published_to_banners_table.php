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
        Schema::table('banners', function (Blueprint $table) {
            $table->boolean('is_published')->default(true)->after('is_active');
        });
        
        // Migrate existing data: assume all currently active are published, and all inactive are drafted.
        \Illuminate\Support\Facades\DB::statement('UPDATE banners SET is_published = is_active');
        // Set all to active by default so they keep showing if they were published
        \Illuminate\Support\Facades\DB::statement('UPDATE banners SET is_active = 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            //
        });
    }
};
