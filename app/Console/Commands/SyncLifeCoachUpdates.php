<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\CoachUpdateService;
use Illuminate\Console\Command;

class SyncLifeCoachUpdates extends Command
{
    protected $signature = 'lifecoach:sync-updates';

    protected $description = 'Sync due Life Coach dashboard updates';

    public function handle(CoachUpdateService $updates): int
    {
        User::role('lifecoach')->each(fn (User $coach) => $updates->sync($coach));
        $this->info('Life Coach updates synced.');

        return self::SUCCESS;
    }
}
