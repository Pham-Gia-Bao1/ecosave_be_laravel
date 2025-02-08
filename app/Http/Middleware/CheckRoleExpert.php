<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
class CheckRoleExpert
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
 
         // Kiểm tra quyền expert
         if ($user->role_id != 3) {
             return response()->json(['message' => 'Your account is not authorized.'], 403);
         }
 
         return $next($request);
    }
}
