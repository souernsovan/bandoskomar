<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Provider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    protected array $programsByCategory = [
        'education-support' => [
            'Scholarship Fund',
            'Back-to-School Kit Drive',
            'Reading Circle',
        ],
        'health-wellness' => [
            'Community Clinic',
            'Nutrition Workshop',
            'Wellness Visit',
        ],
        'emergency-relief' => [
            'Rapid Food Pack',
            'Shelter Support',
            'Crisis Response',
        ],
        'youth-empowerment' => [
            'Youth Mentorship',
            'Digital Skills Lab',
            'Leadership Workshop',
        ],
        'women-family-support' => [
            'Family Resource Center',
            'Safe Space Program',
            'Caregiver Support',
        ],
        'community-development' => [
            'Neighborhood Cleanup',
            'Community Garden',
            'Microgrant Support',
        ],
    ];

    public function run(): void
    {
        $categories = Category::all()->keyBy('slug');
        $providers = Provider::all();

        if ($categories->isEmpty()) {
            $this->command->warn('No impact areas found. Run CategorySeeder first.');
            return;
        }

        if ($providers->isEmpty()) {
            $this->command->warn('No partners found. Run ProviderSeeder first.');
            return;
        }

        $productCount = 0;

        foreach ($categories as $category) {
            $titles = $this->programsByCategory[$category->slug] ?? [$category->name];

            foreach ($titles as $title) {
                $slug = Str::slug($title);
                $uniqueSlug = $slug;
                $counter = 1;
                while (Product::where('slug', $uniqueSlug)->exists()) {
                    $uniqueSlug = $slug . '-' . $counter++;
                }

                $provider = $providers->random();

                Product::updateOrCreate(
                    ['slug' => $uniqueSlug],
                    [
                        'title' => $title,
                        'description' => "{$title} strengthens {$category->name} through community partnership with {$provider->name}.",
                        'image' => null,
                        'status' => 'active',
                       
                    ]
                );

                $productCount++;
            }
        }

        $this->command->info('Programs seeded: ' . $productCount);
    }
}
