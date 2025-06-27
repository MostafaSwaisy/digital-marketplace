<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class JWTAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            // Validate token with User Service
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post('http://localhost:8001/api/auth/validate');

            if (!$response->successful()) {
                return response()->json(['error' => 'Invalid token'], 401);
            }

            $userData = $response->json();

            if (!$userData['valid']) {
                return response()->json(['error' => 'Invalid token'], 401);
            }

            // Add user data to request for use in controllers
            $request->merge(['auth_user' => $userData['user']]);

            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token validation failed'], 401);
        }
    }
}
