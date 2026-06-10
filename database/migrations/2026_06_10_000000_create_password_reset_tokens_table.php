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
        $tableName = env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens');
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->string('email')->index();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'));
    }
};
