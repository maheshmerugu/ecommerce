<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BannerSeeder extends Seeder
{
    /**
     * Hero banners: toy / diecast car images with promotional offers.
     * Promo banners: smaller offer tiles shown below categories.
     */
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('banners');

        $banners = [
            // ── HERO banners (main carousel) ──
            [
                'type'      => 'hero',
                'title'     => 'Mega Toy Car Sale — Up to 40% Off',
                'caption'   => 'Diecast models, RC cars & miniatures at unbeatable prices',
                'link'      => '/products',
                'position'  => 1,
                'image_url' => 'https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?w=1920&h=600&fit=crop&q=80',
                'filename'  => 'hero-toy-1.jpg',
            ],
            [
                'type'      => 'hero',
                'title'     => 'Hot Deals — Diecast & RC Cars',
                'caption'   => 'Premium toy cars for collectors & kids — limited time offers',
                'link'      => '/products',
                'position'  => 2,
                'image_url' => 'https://images.unsplash.com/photo-1587659479294-88f2a4af577a?w=1920&h=600&fit=crop&q=80',
                'filename'  => 'hero-toy-2.jpg',
            ],
            [
                'type'      => 'hero',
                'title'     => 'Weekend Offer — Buy 2 Get 1 Free',
                'caption'   => 'Stock up on toy cars, tracks & accessories this weekend',
                'link'      => '/products',
                'position'  => 3,
                'image_url' => 'https://images.unsplash.com/photo-1519003723042-5e7510c74336?w=1920&h=600&fit=crop&q=80',
                'filename'  => 'hero-toy-3.jpg',
            ],

            // ── PROMO banners (offer tiles) ──
            [
                'type'      => 'promo',
                'title'     => '50% Off — Miniature Models',
                'caption'   => '1:64 & 1:18 scale diecast cars',
                'link'      => '/products',
                'position'  => 1,
                'image_url' => 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=600&h=300&fit=crop&q=80',
                'filename'  => 'promo-toy-models.jpg',
            ],
            [
                'type'      => 'promo',
                'title'     => 'Free Delivery Over ₹999',
                'caption'   => 'On all toy cars & playsets',
                'link'      => '/products',
                'position'  => 2,
                'image_url' => 'https://images.unsplash.com/photo-1617464569877-86742a32876a?w=600&h=300&fit=crop&q=80',
                'filename'  => 'promo-toy-shipping.jpg',
            ],
            [
                'type'      => 'promo',
                'title'     => 'Best Selling Toy Cars',
                'caption'   => 'Top picks loved by kids & collectors',
                'link'      => '/products',
                'position'  => 3,
                'image_url' => 'https://images.unsplash.com/photo-1530124566582-a618bd261556?w=600&h=300&fit=crop&q=80',
                'filename'  => 'promo-toy-bestseller.jpg',
            ],
        ];

        foreach ($banners as $data) {
            $path = 'banners/' . $data['filename'];

            // Always refresh images so toy-car banners replace old real-car photos
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            $this->command->info("Downloading: {$data['filename']}...");
            $imageContent = @file_get_contents($data['image_url']);
            if ($imageContent) {
                Storage::disk('public')->put($path, $imageContent);
                $this->command->info("  ✓ Saved: {$path}");
            } else {
                $this->command->warn("  ✗ Failed to download {$data['image_url']} — skipping.");
                continue;
            }

            Banner::updateOrCreate(
                ['type' => $data['type'], 'position' => $data['position']],
                [
                    'title'     => $data['title'],
                    'caption'   => $data['caption'],
                    'image'     => $path,
                    'link'      => $data['link'],
                    'is_active' => true,
                ]
            );
        }

        // Deactivate old real-car banners no longer in use
        $activeFiles = collect($banners)->pluck('filename')->map(fn ($f) => 'banners/' . $f);
        Banner::whereNotIn('image', $activeFiles)->update(['is_active' => false]);

        $this->command->info('✓ Banner seeder complete — ' . count($banners) . ' toy car banners added.');
    }
}
