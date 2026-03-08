<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class QueueBackpressure
{
    protected const CACHE_KEY = 'queue_backpressure_paused';

    /**
     * Whether the queue driver supports backpressure (pending count is available).
     */
    public static function isSupported(): bool
    {
        return Config::get('queue.default') === 'database';
    }

    /**
     * Current number of pending jobs (only for database driver).
     */
    public static function getPendingCount(): int
    {
        if (Config::get('queue.default') !== 'database') {
            return 0;
        }
        $table = Config::get('queue.connections.database.table', 'jobs');
        return (int) DB::table($table)->count();
    }

    /**
     * True if starters are allowed to dispatch more jobs. When pending >= max_pending we skip
     * until pending drops below resume_below to avoid flip-flopping.
     */
    public static function canDispatch(): bool
    {
        if (! self::isSupported()) {
            return true;
        }

        $maxPending = Config::get('queue.backpressure.max_pending', 0);
        $resumeBelow = Config::get('queue.backpressure.resume_below', 0);

        if ($maxPending <= 0 || $resumeBelow <= 0) {
            return true;
        }

        $pending = self::getPendingCount();

        if ($pending <= $resumeBelow) {
            Cache::forget(self::CACHE_KEY);
            return true;
        }

        if ($pending >= $maxPending) {
            Cache::put(self::CACHE_KEY, true, now()->addMinutes(5));
            return false;
        }

        return ! Cache::get(self::CACHE_KEY, false);
    }
}
