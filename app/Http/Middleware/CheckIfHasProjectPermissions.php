<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckIfHasProjectPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $project = $request->project
            ?? $request->route('project')
            ?? (isset($request->product) ? $request->product->project : null);

        if ($project instanceof Project) {
            if ($request->user()->hasProjectPermissionTo($project, $permission)) {
                return $next($request);
            }
            throw new AccessDeniedHttpException;
        }

        if ($project !== null) {
            $project = Project::find($project);
            if ($project && $request->user()->hasProjectPermissionTo($project, $permission)) {
                return $next($request);
            }
            throw new AccessDeniedHttpException;
        }

        throw new Exception('This route does not contain the project id required to check permissions.', 400);
    }
}
