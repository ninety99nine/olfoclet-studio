<?php

namespace App\Http\Controllers;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Artisan;

class ServerController extends Controller
{
    protected $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function fixServer()
    {
        /**
         *  Get the full path to show projects.
         *
         *  This is because once we run the "handle:server-errors" command,
         *  we won't be able to run the redirect() using "named routes"
         *  immediately after running the command. So we need to get
         *  the full path now before running the command, then we
         *  redirect to the full path specified.
         */
        $projectsRoute = route('show.projects');

        // Run this artisan command to clear common Laravel server issues
        Artisan::call('handle:server-errors');

        return redirect($projectsRoute);
    }
}
