<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OnlyToysCarsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Remove existing category associations and categories
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        if (Schema::hasTable('product_categories')) {
            DB::table('product_categories')->truncate();
        }
        if (Schema::hasTable('categories')) {
            DB::table('categories')->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create only the Toys Cars category
        Category::create([
            'name' => 'Toys Cars',
            'description' => 'Toy cars and vehicles',
            'status' => true,
        ]);
    }
}
