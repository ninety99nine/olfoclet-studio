<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InspectDatabase extends Command
{
    protected $signature = 'db:inspect {--tables : List all tables with row counts}';

    protected $description = 'Inspect database schema and key tables for debugging (no credentials exposed)';

    public function handle(): int
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");

        $this->info("Database: {$database} (connection: {$connection})");
        $this->newLine();

        if ($this->option('tables')) {
            $this->inspectTables();
            return 0;
        }

        $this->inspectJobs();
        $this->inspectJobBatches();
        $this->inspectAutoBilling();
        $this->inspectSmsCampaigns();
        $this->inspectSubscribers();

        return 0;
    }

    protected function inspectTables(): void
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");
        $tables = DB::select('SHOW TABLES');
        $key = 'Tables_in_' . $database;
        $this->info('--- Tables and row counts ---');
        foreach ($tables as $row) {
            $table = $row->{$key};
            $count = DB::table($table)->count();
            $this->line("  {$table}: {$count}");
        }
    }

    protected function inspectJobs(): void
    {
        $this->info('--- jobs (queue) ---');
        if (! Schema::hasTable('jobs')) {
            $this->warn('  Table jobs does not exist.');
            return;
        }
        $total = DB::table('jobs')->count();
        $byQueue = DB::table('jobs')->select('queue')->selectRaw('count(*) as c')->groupBy('queue')->get();
        $this->line("  Total pending: {$total}");
        foreach ($byQueue as $row) {
            $this->line("    queue [{$row->queue}]: {$row->c}");
        }
        if ($total > 0) {
            $sample = DB::table('jobs')->select('id', 'queue', 'created_at')->limit(3)->get();
            foreach ($sample as $j) {
                $this->line("    Sample: id={$j->id} queue={$j->queue} created_at={$j->created_at}");
            }
        }
        $this->newLine();
    }

    protected function inspectJobBatches(): void
    {
        $this->info('--- job_batches ---');
        if (! Schema::hasTable('job_batches')) {
            $this->warn('  Table job_batches does not exist.');
            return;
        }
        $total = DB::table('job_batches')->count();
        $pending = DB::table('job_batches')->whereNull('finished_at')->count();
        $cancelled = DB::table('job_batches')->whereNotNull('cancelled_at')->count();
        $this->line("  Total: {$total} | Pending (not finished): {$pending} | Cancelled: {$cancelled}");
        if ($total > 0) {
            $recent = DB::table('job_batches')->orderByDesc('created_at')->limit(3)->get(['id', 'name', 'total_jobs', 'pending_jobs', 'failed_jobs', 'created_at', 'finished_at']);
            foreach ($recent as $b) {
                $this->line("    {$b->name} | total={$b->total_jobs} pending={$b->pending_jobs} failed={$b->failed_jobs} | created={$b->created_at} finished=" . ($b->finished_at ?? 'null'));
            }
        }
        $this->newLine();
    }

    protected function inspectAutoBilling(): void
    {
        $this->info('--- auto_billing (schedules, job_batches) ---');
        foreach (['auto_billing_schedules', 'auto_billing_job_batches'] as $table) {
            if (! Schema::hasTable($table)) {
                $this->warn("  {$table}: does not exist.");
                continue;
            }
            $count = DB::table($table)->count();
            $this->line("  {$table}: {$count}");
        }
        if (Schema::hasTable('auto_billing_schedules')) {
            $enabled = DB::table('auto_billing_schedules')->where('auto_billing_enabled', '1')->count();
            $this->line("  auto_billing_schedules with auto_billing_enabled=1: {$enabled}");
        }
        $this->newLine();
    }

    protected function inspectSmsCampaigns(): void
    {
        $this->info('--- sms_campaigns / sms_campaign_schedules / sms_campaign_job_batches ---');
        foreach (['sms_campaigns', 'sms_campaign_schedules', 'sms_campaign_job_batches'] as $table) {
            if (! Schema::hasTable($table)) {
                $this->warn("  {$table}: does not exist.");
                continue;
            }
            $count = DB::table($table)->count();
            $this->line("  {$table}: {$count}");
        }
        $this->newLine();
    }

    protected function inspectSubscribers(): void
    {
        $this->info('--- subscribers ---');
        if (! Schema::hasTable('subscribers')) {
            $this->warn('  Table subscribers does not exist.');
            return;
        }
        $count = DB::table('subscribers')->count();
        $this->line("  Total: {$count}");
        $this->newLine();
    }
}
