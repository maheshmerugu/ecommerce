<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BannerSeeder extends Seeder
{
    /**
     * Hero banners: full-width car images for the super-sale carousel.
     * Promo banners: smaller offer tiles shown below categories.
     *
     * Images are downloaded from Unsplash (free, no API key needed).
     */
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('banners');

        $banners = [
            // ── HERO banners (main carousel) ──
            [
                'type'      => 'hero',
                'title'     => 'Super Sale — Up to 40% Off',
                'caption'   => 'Shop the best deals on SUVs, Sedans & Hatchbacks',
                'link'      => '/products',
                'position'  => 1,
                'image_url' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1920&h=600&fit=crop&q=80',
                'filename'  => 'hero-car-1.jpg',
            ],
            [
                'type'      => 'hero',
                'title'     => 'New Arrivals — Luxury Cars',
                'caption'   => 'Experience premium performance & style',
                'link'      => '/products',
                'position'  => 2,
                'image_url' => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=1920&h=600&fit=crop&q=80',
                'filename'  => 'hero-car-2.jpg',
            ],
            [
                'type'      => 'hero',
                'title'     => 'Weekend Deals — Drive Your Dream',
                'caption'   => 'Exclusive offers this weekend only',
                'link'      => '/products',
                'position'  => 3,
                'image_url' => 'https://images.unsplash.com/photo-1493238792000-8113da705763?w=1920&h=600&fit=crop&q=80',
                'filename'  => 'hero-car-3.jpg',
            ],

            // ── PROMO banners (offer tiles) ──
            [
                'type'      => 'promo',
                'title'     => '50% Off — Accessories',
                'caption'   => 'Car mats, covers & more',
                'link'      => '/products',
                'position'  => 1,
                'image_url' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&h=300&fit=crop&q=80',
                'filename'  => 'promo-accessories.jpg',
            ],
            [
                'type'      => 'promo',
                'title'     => 'Free Shipping Over ₹5000',
                'caption'   => 'On all car parts & accessories',
                'link'      => '/products',
                'position'  => 2,
                'image_url' => 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=600&h=300&fit=crop&q=80',
                'filename'  => 'promo-shipping.jpg',
            ],
            [
                'type'      => 'promo',
                'title'     => 'Best Sellers',
                'caption'   => 'Top-rated models at great prices',
                'link'      => '/products',
                'position'  => 3,
                'image_url' => 'https://images.unsplash.com/photo-1471444928139-48c5bf5173f8?w=600&h=300&fit=crop&q=80',
                'filename'  => 'promo-bestseller.jpg',
            ],
        ];

        foreach ($banners as $data) {
            $path = 'banners/' . $data['filename'];

            // Skip download if image already exists
            if (!Storage::disk('public')->exists($path)) {
                $this->command->info("Downloading: {$data['filename']}...");
                $imageContent = @file_get_contents($data['image_url']);
                if ($imageContent) {
                    Storage::disk('public')->put($path, $imageContent);
                    $this->command->info("  ✓ Saved: {$path}");
                } else {
                    $this->command->warn("  ✗ Failed to download {$data['image_url']} — skipping.");
                    continue;
                }
            } else {
                $this->command->info("Already exists: {$path}");
            }

            Banner::updateOrCreate(
                ['title' => $data['title'], 'type' => $data['type']],
                [
                    'caption'   => $data['caption'],
                    'image'     => $path,
                    'link'      => $data['link'],
                    'position'  => $data['position'],
                    'type'      => $data['type'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✓ Banner seeder complete — ' . count($banners) . ' banners added.');
    }
}
