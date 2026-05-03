<?php

namespace Database\Seeders;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run admin seeder
        $this->call([
            PermissionsSeeder::class,
            UsersSeeder::class,
            SiteSettingSeeder::class,
            PageSeeder::class,
            CategorySeeder::class,
            ProviderSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
