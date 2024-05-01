<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HandleServerErrors extends Command
{
    protected $signature = 'handle:server-errors';

    protected $description = 'Handle server errors by performing necessary actions';

    public function handle()
    {
        // Clearing configuration, routes, views, and cache
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');
        $this->call('cache:clear');

        // Caching configuration, routes, views, and events
        $this->call('config:cache');    // Cache configuration for improved performance
        $this->call('route:cache');     // Cache route definitions for faster route resolution
        $this->call('view:cache');      // Cache compiled Blade views for faster view rendering
        $this->call('event:cache');     // Cache event listeners for faster event handling
    }
}
