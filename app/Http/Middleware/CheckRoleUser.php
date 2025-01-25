<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;

class CheckRoleUser
{
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra xem token có tồn tại không
        $token = $request->header('Authorization');
        if (!$token) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        try {
            // Giải mã và xác thực token
            $user = JWTAuth::parseToken()->authenticate($token);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['message' => 'Token has expired.'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['message' => 'Token is invalid.'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['message' => 'Token is absent.'], 401);
        }

        // Kiểm tra quyền admin
        if ($user->role_id != 2) {
            return response()->json(['message' => 'Your account is not authorized.'], 403);
        }

        return $next($request);
    }
}
