<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    public function up(): void
    {
        Setting::set('store_name', config('app.name', 'Wheels Cars'), 'store');
        Cache::forget('setting:store_name');
    }

    public function down(): void
    {
        // no-op
    }
};
