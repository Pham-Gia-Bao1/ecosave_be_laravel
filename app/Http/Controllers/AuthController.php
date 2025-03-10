<?php

namespace App\Http\Controllers;

use App\Models\ExpertDetail;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */

     public function register(Request $request)
     {
         // Kiểm tra thông tin người dùng cho các vai trò khác
         $validator = Validator::make($request->all(), [
             'name' => 'nullable',
             'email' => 'required|email|unique:users,email',
             'password' => 'required|string|confirmed|min:6|max:25',
             'address' => 'required|string',
             'latitude' => 'required',
             'longitude' => 'required',
             'role_id' => 'required|exists:roles,id',
             // Thêm các trường cần thiết cho Store nếu role_id = 3
             'store_name' => 'required_if:role_id,3|string|max:255',
             'avatar' => 'nullable|string|max:255',
             'store_type' => 'required_if:role_id,3|string|max:100',
             'opening_hours' => 'nullable|string|max:255',
             'description' => 'nullable|string',
         ]);

         if ($validator->fails()) {
             return response()->json($validator->errors()->toJson(), 400);
         }

         // Tạo người dùng mới
         $user = User::create([
             'username' => $request->name, // Thay 'name' thành 'username'
             'email' => $request->email,
             'email_verified_at' => now(),
             'password' => bcrypt($request->password),
             'address' => $request->address, // Địa chỉ mặc định trống
             'avatar' => asset('assets/img/avatar/avatar-4.png'), // Thay 'profile_picture' thành 'avatar'
             'is_active' => true, // Sử dụng 'is_active' thay vì 'status'
             'role' => $request->role_id, // Thay 'role_id' thành 'role'
             'phone_number' => '', // Số điện thoại mặc định trống
             'latitude' => $request->latitude, // Mặc định giá trị null
             'longitude' => $request->longitude, // Mặc định giá trị null
             'created_at' => now(),
             'updated_at' => now(),
         ]);

         // Nếu role_id là 3 thì tạo thông tin cửa hàng
         if ($request->role_id == 3) {
             $store = Store::create([
                 'store_name' => $request->store_name,
                 'avatar' => $request->avatar ?? 'https://via.placeholder.com/200x200', // Avatar mặc định
                 'logo' => $request->logo ?? 'https://via.placeholder.com/200x200', // Avatar mặc định
                 'store_type' => $request->store_type,
                 'opening_hours' => $request->opening_hours,
                 'status' => 'active', // Mặc định trạng thái là active
                 'contact_email' => $request->email,
                 'contact_phone' => $request->phone_number ?? null,
                 'latitude' => $request->latitude,
                 'longitude' => $request->longitude,
                 'address' => $request->address,
                 'soft_description' => $request->soft_description,
                 'description' => $request->description,
                 'user_id' => $user->id, // Liên kết với user vừa tạo
                 'created_at' => now(),
                 'updated_at' => now(),
             ]);
         }

         return response()->json([
             'message' => 'User successfully registered',
             'user' => $user,
             'store' => $request->role_id == 3 ? $store : null, // Nếu có tạo store thì trả về thông tin
         ], 201);
     }



    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }


    public function refresh(Request $request)
    {
        $refreshToken = $request->header('Authorization');

        if (!$refreshToken) {
            return response()->json(['error' => 'Refresh token is required'], 400);
        }

        try {
            // Xác thực refresh token
            $decodedToken = auth()->setToken(str_replace('Bearer ', '', $refreshToken))->checkOrFail();

            if (!isset($decodedToken['refresh']) || !$decodedToken['refresh']) {
                return response()->json(['error' => 'Invalid refresh token'], 401);
            }

            // Tạo access token mới
            $newToken = auth()->tokenById(auth()->user()->id);

            return $this->createNewToken($newToken);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Refresh token expired or invalid'], 401);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function createNewToken($token)
    {
        $refreshToken = auth()->claims(['refresh' => true])->setTTL(20160)->tokenById(auth()->user()->id); // Refresh token TTL = 14 ngày (20160 phút)

        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function changePassWord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|confirmed|min:6',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $userId = auth()->user()->id;

        $user = User::where('id', $userId)->update(

            ['password' => bcrypt($request->new_password)]
        );

        return response()->json([
            'message' => 'User successfully changed password',
            'user' => $user,
        ], 201);
    }
}
