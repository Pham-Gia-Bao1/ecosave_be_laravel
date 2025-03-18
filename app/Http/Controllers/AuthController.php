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
        $this->middleware('auth:api', ['except' => ['login', 'register', 'checkEmail']]);
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
    // Kiểm tra thông tin đầu vào
    $validator = Validator::make($request->all(), [
        'name' => 'nullable',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|confirmed|min:6|max:25',
        'address' => 'required|string',
        'latitude' => 'required',
        'longitude' => 'required',
        'role_id' => 'required|exists:roles,id',
        'avatar' => 'nullable|string|max:255', // Cho phép gửi avatar từ request
        'store_name' => 'required_if:role_id,3|string|max:255',
        'store_type' => 'required_if:role_id,3|string|max:100',
        'opening_hours' => 'nullable|string|max:255',
        'description' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors()->toJson(), 400);
    }

    // Xử lý avatar: Nếu không có, dùng ảnh mặc định
    $userAvatar = $request->avatar ?? asset('assets/img/avatar/avatar-4.png');

    // Tạo người dùng mới
    $user = User::create([
        'username' => $request->name,
        'email' => $request->email,
        'email_verified_at' => now(),
        'password' => bcrypt($request->password),
        'address' => $request->address,
        'avatar' => $userAvatar, // Lưu avatar của user
        'is_active' => true,
        'role' => $request->role_id,
        'phone_number' => '',
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Nếu role_id là 3 thì tạo thông tin cửa hàng
    if ($request->role_id == 3) {
        $store = Store::create([
            'store_name' => $request->store_name,
            'avatar' => $request->avatar ?? 'https://via.placeholder.com/200x200', // Dùng avatar nếu có
            'logo' => $request->avatar ?? 'https://via.placeholder.com/200x200', // Dùng avatar làm logo nếu không có logo
            'store_type' => $request->store_type,
            'opening_hours' => $request->opening_hours,
            'status' => 'active',
            'contact_email' => $request->email,
            'contact_phone' => $request->phone_number ?? null,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'soft_description' => $request->soft_description,
            'description' => $request->description,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return response()->json([
        'message' => 'User successfully registered',
        'user' => $user,
        'store' => $request->role_id == 3 ? $store : null,
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

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        return response()->json([
            'exists' => $user ? true : false
        ]);
    }
}
