<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResetStuckProcessingLocks extends Seeder
{
    /**
     * Run the database seeds.
     * This clears atomic locks that might have been left 'true' during a crash.
     */
    public function run(): void
    {
        // Clear Billing Locks
        DB::table('auto_billing_schedules')->update([
            'is_processing' => false,
            'processing_token' => null,
            'updated_at' => now(),
        ]);

        // Clear SMS Locks
        DB::table('sms_campaign_schedules')->update([
            'is_processing' => false,
            'processing_token' => null,
            'updated_at' => now(),
        ]);

        $this->command->info('Cleaned up all stale processing locks.');
    }
}
