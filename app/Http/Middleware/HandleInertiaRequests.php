<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Models\Project;
use Inertia\Middleware;
use Illuminate\Http\Request;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $projectPermissions = [];
        $projectData = null;

        // If the project is provided on the request
        if ($request->route('project')) {

            /** @var User $user */
            $user = auth()->user();

            if ($user) {

                // Get the project ID from route parameter (numeric ID or Project instance)
                $projectId = $request->route('project') instanceof Project
                    ? $request->route('project')->id
                    : $request->route('project');

                // Get the project from the user's projectsAsTeamMember relationship
                /** @var Project $project */
                $project = $user->projectsAsTeamMember()
                    ->where('project_id', $projectId)
                    ->first();

                if ($project) {
                    $projectPermissions = $project->pivot->permissions;
                    $projectData = [
                        'id' => $project->id,
                        'name' => $project->name,
                        'secret_token' => $project->secret_token, // Share secret_token
                    ];
                }

            }

        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'project' => $projectData,
            'projectPermissions' => $projectPermissions,
        ]);
    }
}
