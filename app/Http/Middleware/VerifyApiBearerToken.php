<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class VerifyApiBearerToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //  Get the Bearer Token
        $bearerToken = $request->bearerToken();

        if(!empty($bearerToken) && $bearerToken == config('app.API_BEARER_TOKEN')) {
            $isValid = true;
        }else{
            $project = Project::findOrFail(request()->project);
            if($bearerToken == $project->secret_token) {
                $isValid = true;
            }else{
                $isValid = false;
            }
        }

        //  Validate the Bearer Token
        if($isValid) {

            //  Allow request to proceed
            return $next($request);

        }

        //  Deny access
        throw new AccessDeniedHttpException;
    }
}
