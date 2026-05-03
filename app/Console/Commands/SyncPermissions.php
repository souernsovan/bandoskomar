<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class SyncPermissions extends Command
{
    protected $signature = 'permissions:sync {--guard=web : The guard name}';

    protected $description = 'Sync permissions from config/permissions.php';

    public function handle(): int
    {
        $guard = $this->option('guard');

        foreach (config('permissions', []) as $group => $permissions) {
            foreach (array_keys($permissions) as $name) {
                Permission::findOrCreate($name, $guard);
                $this->line("  <info>✓</info> {$name}");
            }
        }

        $this->info('Permissions synced successfully.');

        return self::SUCCESS;
    }
}
