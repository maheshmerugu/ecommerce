<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pincode_shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->string('pincode', 6);
            $table->enum('match_type', ['exact', 'prefix'])->default('exact');
            $table->decimal('shipping_charge', 10, 2);
            $table->string('label')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['pincode', 'match_type']);
            $table->index(['match_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pincode_shipping_rates');
    }
};
