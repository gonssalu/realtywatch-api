<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCharacteristicOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $crc = $request->route('characteristic');
        if ($request->user()->id != $crc->user_id) {
            return response(
                [
                    'message' => 'You are not authorized to access this characteristic',
                ],
                403
            );
        }

        return $next($request);
    }
}
