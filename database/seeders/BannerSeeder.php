<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class BannerSeeder extends Seeder
{
    /**
     * Hero banners: 5 carousel slides for the homepage.
     */
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('banners');

        $banners = [
            [
                'type'      => 'hero',
                'title'     => 'Super Sale — Up to 40% Off',
                'caption'   => 'Shop premium toy cars, diecast models & RC vehicles',
                'link'      => '/products',
                'position'  => 1,
                'image_url' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=1920&h=600&fit=crop&q=80',
                'filename'  => 'hero-car-1.jpg',
            ],
            [
                'type'      => 'hero',
                'title'     => 'New Arrivals — Hot Wheels & More',
                'caption'   => 'Latest car models for kids and collectors',
                'link'      => '/products',
                'position'  => 2,
                'image_url' => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=1920&h=600&fit=crop&q=80',
                'filename'  => 'hero-car-2.jpg',
            ],
            [
                'type'      => 'hero',
                'title'     => 'Weekend Deals — Drive Your Dream',
                'caption'   => 'Exclusive offers on top-selling car collections',
                'link'      => '/products',
                'position'  => 3,
                'image_url' => 'https://images.unsplash.com/photo-1493238792000-8113da705763?w=1920&h=600&fit=crop&q=80',
                'filename'  => 'hero-car-3.jpg',
            ],
            [
                'type'      => 'hero',
                'title'     => 'Kids Collection — Toy Cars & Playsets',
                'caption'   => 'Colorful toy cars perfect for little drivers — ages 3+',
                'link'      => '/products',
                'position'  => 4,
                'image_url' => 'https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?w=1920&h=600&fit=crop&q=80',
                'filename'  => 'hero-kids-1.jpg',
            ],
            [
                'type'      => 'hero',
                'title'     => 'RC & Diecast — Build Your Garage',
                'caption'   => 'Remote-control cars, 1:64 models & track sets — shop now',
                'link'      => '/products',
                'position'  => 5,
                'image_url' => 'https://images.unsplash.com/photo-1471444928139-48c5bf5173f8?w=1920&h=600&fit=crop&q=80',
                'filename'  => 'hero-car-5.jpg',
            ],
        ];

        foreach ($banners as $data) {
            $path = 'banners/' . $data['filename'];

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
                ['type' => 'hero', 'position' => $data['position']],
                [
                    'title'     => $data['title'],
                    'caption'   => $data['caption'],
                    'image'     => $path,
                    'link'      => $data['link'],
                    'is_active' => true,
                ]
            );
        }

        // Deactivate any old hero banners not in this set
        $activeFiles = collect($banners)->pluck('filename')->map(fn ($f) => 'banners/' . $f);
        Banner::where('type', 'hero')->whereNotIn('image', $activeFiles)->update(['is_active' => false]);

        $this->command->info('✓ Banner seeder complete — ' . count($banners) . ' hero banners added.');
    }
}
