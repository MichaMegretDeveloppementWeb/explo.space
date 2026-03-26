<?php

namespace App\Console\Commands;

use App\Services\Admin\Autofill\AutofillCleanupService;
use Illuminate\Console\Command;

class CleanupAutofillCommand extends Command
{
    protected $signature = 'autofill:cleanup';

    protected $description = 'Clean up orphaned autofill temp files';

    public function handle(AutofillCleanupService $cleanupService): int
    {
        $cleaned = $cleanupService->cleanupOrphanedTempFiles();

        $this->info("Cleaned up {$cleaned} orphaned temp director".($cleaned === 1 ? 'y' : 'ies').'.');

        return self::SUCCESS;
    }
}
