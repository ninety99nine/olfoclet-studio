<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClearStaleSmsLocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:clear-stale-locks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release SMS campaign locks for zombie jobs older than the retry cycle.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /**
         * MATH: 3 tries * 1 hour retryAfter = 3 hours.
         * We set threshold to 4 hours to be safe. If a job has been
         * "processing" for 4 hours, it's definitely a zombie.
         */
        $staleThreshold = now()->subHours(4);

        $affected = DB::table('sms_campaign_schedules')
            ->where('is_processing', true)
            ->where('updated_at', '<', $staleThreshold)
            ->update([
                'is_processing' => false,
                'processing_token' => null, // Invalidates any late-arriving zombie jobs
                'updated_at'    => now(),
            ]);

        if ($affected > 0) {
            Log::warning("Janitor: Cleaned up {$affected} stale SMS campaign locks.");
            $this->info("Successfully released {$affected} stale locks.");
        } else {
            $this->comment("No stale SMS locks found.");
        }

        return 0;
    }
}
