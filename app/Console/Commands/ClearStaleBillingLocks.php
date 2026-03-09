<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClearStaleBillingLocks extends Command
{
    protected $signature = 'billing:clear-stale-locks';
    protected $description = 'Release auto-billing locks for zombie jobs older than the retry cycle.';

    public function handle()
    {
        /**
         * MATH: 3 tries * 1 hour retryAfter = 3 hours.
         * We set threshold to 4 hours to be safe.
         */
        $staleThreshold = now()->subHours(4);

        $affected = DB::table('auto_billing_schedules')
            ->where('is_processing', true)
            ->where('updated_at', '<', $staleThreshold)
            ->update([
                'is_processing' => false,
                'processing_token' => null, // This invalidates any late-arriving zombie jobs
                'updated_at'    => now(),
            ]);

        if ($affected > 0) {
            Log::warning("Janitor: Cleaned up {$affected} stale billing locks.");
            $this->info("Successfully released {$affected} stale locks.");
        } else {
            $this->comment("No stale locks found.");
        }
    }
}
