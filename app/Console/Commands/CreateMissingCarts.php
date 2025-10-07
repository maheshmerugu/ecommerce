<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;

class CreateMissingCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:create-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create carts for customers who don\'t have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $customersWithoutCarts = Customer::whereDoesntHave('carts')->get();
        
        $this->info("Found {$customersWithoutCarts->count()} customers without carts.");
        
        $created = 0;
        foreach ($customersWithoutCarts as $customer) {
            $cart = $customer->getOrCreateCart();
            $this->info("Created cart (ID: {$cart->id}) for customer: {$customer->first_name} {$customer->last_name} (ID: {$customer->id})");
            $created++;
        }
        
        $this->info("Successfully created {$created} carts.");
        
        return 0;
    }
}