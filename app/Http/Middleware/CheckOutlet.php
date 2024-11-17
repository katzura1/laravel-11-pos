<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOutlet
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $outletId = session()->get('outlet_id');
        // dd($outletId);
        if (!$outletId) {
            return $request->expectsJson()
            ? response()->json(['message' => 'Please select your outlet first.'], Response::HTTP_FORBIDDEN)
            : redirect()->route('choose-outlet');
        }

        return $next($request);
    }
}
