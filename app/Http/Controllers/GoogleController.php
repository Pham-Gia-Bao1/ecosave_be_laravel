<?php
// app/Http/Controllers/GoogleController.php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
class GoogleController extends Controller
{
    // Handle the callback from Google
    public function handleGoogleCallback(Request $request)
    {
        $googleToken = $request->input('token');
        // Google public keys URL
        $keysUrl = 'https://www.googleapis.com/oauth2/v3/certs';
        // Fetch Google public keys
        $client = new Client();
        $response = $client->get($keysUrl);
        $keys = json_decode($response->getBody()->getContents(), true);
        // Decode the token
        $decodedToken = null;
        try {
            $publicKeys = JWK::parseKeySet($keys); // Create an array of public keys
            $decodedToken = JWT::decode($googleToken, $publicKeys);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Token is invalid'], 400);
        }
        $email = $decodedToken->email;
        $user = User::where('email', $email)->first();
        // If the user doesn't exist, create a new user
        if (!$user) {
            $user = User::create([
                'name' => $decodedToken->name ?? 'Google User',
                'email' => $email,
                'password' => bcrypt($email), // Use a dummy password
                'address' => '',
                'profile_picture' => $decodedToken->picture ?? asset('assets/images/avatar-1.avif'),
                'date_of_birth' => null,
                'phone_number' => '',
                'gender' => '',
                'status' => true,
                'role_id' => 2, // Default role id; adjust based on your needs
            ]);
        }
        // Simulate a request to the login function
        $loginRequest = new Request([
            'email' => $user->email,
            'password' => $user->email, // Use the dummy password
        ]);
        // return response()->json($loginRequest);
        return $this->login($loginRequest);
    }
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
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
