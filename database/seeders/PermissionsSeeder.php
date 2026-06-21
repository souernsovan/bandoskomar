<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\PermissionBootstrapper;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionBootstrapper::class)->syncFromConfig();
    }
}
