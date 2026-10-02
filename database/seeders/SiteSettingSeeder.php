<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'group' => 'global',
                'value' => 'Bandos Komar',
            ],
            [
                'key' => 'site_description',
                'group' => 'global',
                'value' => 'Bandos Komar (BK) is a local NGO dedicated to improving education in Cambodia, especially in rural areas.',
            ],
            [
                'key' => 'site_logo',
                'group' => 'global',
                'value' => SiteSetting::DEFAULT_SITE_LOGO,
            ],
            [
                'key' => 'site_icon',
                'group' => 'global',
                'value' => SiteSetting::DEFAULT_SITE_ICON,
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'group' => $setting['group'],
                    'value' => $setting['value'],
                ]
            );
        }

        $this->command->info('Site settings seeded successfully!');
        $this->command->info('Total settings created: '.count($settings));
    }
}
