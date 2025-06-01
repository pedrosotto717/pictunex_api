
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

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
        try {
            $token = $request->header('Authorization-Token');
            
            if (!$token) {
                return response()->json(['error' => 'Token not provided'], 401);
            }

            // Remove 'Bearer ' prefix if present
            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }

        } catch (JWTException $e) {
            return response()->json(['error' => 'Token invalid'], 401);
        }

        return $next($request);
    }
}
