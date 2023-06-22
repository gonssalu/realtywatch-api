<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPropertyListOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $propertyList = $request->route('propertyList');

        if ($request->user()->id != $propertyList->user_id) {
            return response(
                [
                    'message' => 'You are not authorized to access this collection',
                ],
                403
            );
        }

        return $next($request);
    }
}
