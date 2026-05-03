<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Education Support',
                'description' => 'Scholarships, school supplies, and learning support for children and young people.',
            ],
            [
                'name' => 'Health & Wellness',
                'description' => 'Community health outreach, wellness education, and family care support.',
            ],
            [
                'name' => 'Emergency Relief',
                'description' => 'Fast-response aid for families affected by crisis, displacement, or urgent hardship.',
            ],
            [
                'name' => 'Youth Empowerment',
                'description' => 'Mentorship, skills training, and leadership opportunities for young people.',
            ],
            [
                'name' => 'Women & Family Support',
                'description' => 'Safe spaces, family resources, and practical care for women and caregivers.',
            ],
            [
                'name' => 'Community Development',
                'description' => 'Local projects that strengthen neighborhoods through shared action.',
            ],
        ];

        foreach ($categories as $item) {
            Category::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'status' => 'active',
                    'image' => null,
                ]
            );
        }

        $this->command->info('Impact areas seeded successfully!');
        $this->command->info('Total impact areas: ' . count($categories));
    }
}
