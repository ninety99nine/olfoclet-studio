<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Artisan;

class ServerController extends Controller
{
    protected $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function showServer() {

        //  Render the server view
        return Inertia::render('Server/Main');

    }

    public function viewClear()
    {
        Artisan::call('view:clear');
        return redirect()->back()->with('message', 'View cleared successfully');
    }

    public function routeClear()
    {
        Artisan::call('route:clear');
        return redirect()->back()->with('message', 'Route cleared successfully');
    }

    public function configClear()
    {
        Artisan::call('config:clear');
        return redirect()->back()->with('message', 'Config cleared successfully');
    }

    public function cacheClear()
    {
        Artisan::call('cache:clear');
        return redirect()->back()->with('message', 'Cache cleared successfully');
    }

    public function viewCache()
    {
        Artisan::call('view:cache');
        return redirect()->back()->with('message', 'View cached successfully');
    }

    public function eventCache()
    {
        Artisan::call('event:cache');
        return redirect()->back()->with('message', 'Event cached successfully');
    }

    public function routeCache()
    {
        Artisan::call('route:cache');
        return redirect()->back()->with('message', 'Route cached successfully');
    }

    public function configCache()
    {
        Artisan::call('config:cache');
        return redirect()->back()->with('message', 'Config cached successfully');
    }

    public function optimize()
    {
        Artisan::call('optimize');
        return redirect()->back()->with('message', 'Optimized successfully');
    }

    public function handleServerErrors()
    {
        /**
         *  Get the full path to show server.
         *
         *  This is because once we run the "handle:server-errors" command,
         *  we won't be able to run the redirect() using "named routes"
         *  immediately after running the command. So we need to get
         *  the full path now before running the command, then we
         *  redirect to the full path specified.
         */
        if( request()->filled('project') ) {

            $showServerRoute = route('show.server', ['project' => request()->query('project')]);

        }else{

            $showServerRoute = route('show.server');

        }

        // Run this artisan command to clear common Laravel server issues
        Artisan::call('handle:server-errors');

        return redirect($showServerRoute);
    }
}
