<?php

namespace App\Http\Middleware;

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
        //  Check if the Bearer Token has been provided
        if($bearerToken = $request->bearerToken()) {

            //  Validate the provided Bearer Token
            if($bearerToken == config('app.API_BEARER_TOKEN')) {

                //  Allow request to proceed
                return $next($request);

            }

        }

        //  Deny access
        throw new AccessDeniedHttpException;
    }
}
