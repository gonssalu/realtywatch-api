<?php

namespace App\Http\Middleware;

use App\Models\Property;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPropertyOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $property = $request->route('property');
        if ($property === null) {
            $property = Property::withTrashed()->whereId($request->route('trashedProperty'))->first();
            if ($property === null)
                return response(
                    [
                        'message' => 'Property not found',
                    ],
                    404
                );
        }

        if ($request->user()->id != $property->user_id) {
            return response(
                [
                    'message' => 'You are not authorized to access this property',
                ],
                403
            );
        }

        return $next($request);
    }
}
