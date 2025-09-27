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
        Schema::table('orders', function (Blueprint $table) {
            // Add Razorpay integration fields
            $table->string('razorpay_order_id')->nullable()->after('payment_method');
            $table->string('razorpay_payment_id')->nullable()->after('razorpay_order_id');
            $table->string('razorpay_signature')->nullable()->after('razorpay_payment_id');
            
            // Add session_id for guest orders
            $table->string('session_id')->nullable()->after('customer_id');
            
            // Add customer details for guest orders
            $table->string('customer_name')->nullable()->after('session_id');
            $table->string('customer_email')->nullable()->after('customer_name');  
            $table->string('customer_phone')->nullable()->after('customer_email');
            
            // Add index for razorpay order id
            $table->index('razorpay_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['razorpay_order_id']);
            $table->dropColumn([
                'razorpay_order_id',
                'razorpay_payment_id', 
                'razorpay_signature',
                'session_id',
                'customer_name',
                'customer_email',
                'customer_phone'
            ]);
        });
    }
};
