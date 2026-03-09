<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Project;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    /**
     * Show the analytics dashboard for the project.
     */
    public function showAnalytics(Project $project): Response
    {
        return Inertia::render('Analytics/Main');
    }
}
