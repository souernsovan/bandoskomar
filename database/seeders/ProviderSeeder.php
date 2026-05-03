<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            ['name' => 'Local Partners Network', 'slug' => 'local-partners-network'],
            ['name' => 'Volunteer Circle', 'slug' => 'volunteer-circle'],
            ['name' => 'Health Outreach Alliance', 'slug' => 'health-outreach-alliance'],
            ['name' => 'School Support Collective', 'slug' => 'school-support-collective'],
            ['name' => 'Relief Response Team', 'slug' => 'relief-response-team'],
            ['name' => 'Donor Circle', 'slug' => 'donor-circle'],
        ];

        foreach ($providers as $provider) {
            Provider::updateOrCreate(
                ['slug' => trim($provider['slug'])],
                [
                    'name' => $provider['name'],
                    'image' => null,
                ]
            );
        }

        $this->command->info('Partners seeded: ' . count($providers));
    }
}
