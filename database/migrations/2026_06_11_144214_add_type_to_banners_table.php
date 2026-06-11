<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            // 'hero'  = main super-sale carousel (full-width, top of homepage)
            // 'promo' = promotional offer banners (smaller, below categories)
            $table->enum('type', ['hero', 'promo'])->default('hero')->after('position');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
