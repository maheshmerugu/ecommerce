<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_settings', function (Blueprint $table) {
            $table->id();
            $table->string('mailer')->default('smtp');
            $table->string('host')->default('smtp.gmail.com');
            $table->unsignedSmallInteger('port')->default(587);
            $table->string('encryption')->default('tls');   // tls | ssl | none
            $table->string('username')->nullable();          // Gmail address
            $table->text('password')->nullable();            // App password (encrypted)
            $table->string('from_address')->nullable();
            $table->string('from_name')->nullable();
            $table->boolean('is_active')->default(false);   // becomes true once configured
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_settings');
    }
};
