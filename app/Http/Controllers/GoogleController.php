<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Google_Client;
use Illuminate\Support\Str;
use Log;

class GoogleController extends Controller
{
    public function handleGoogleCallback(Request $request)
    {
        $googleToken = $request->input('token');

        if (!$googleToken) {
            return response()->json(['message' => 'Token is required'], 400);
        }
        echo $googleToken;



        try {
            // Fetch Google public keys to validate the token
            $keysUrl = 'https://www.googleapis.com/oauth2/v3/certs';
            $response = (new Client())->get($keysUrl);
            $keys = json_decode($response->getBody()->getContents(), true);
            $publicKeys = JWK::parseKeySet($keys);

            // Decode and validate the token
            $decodedToken = JWT::decode($googleToken, $publicKeys);

            // Extract user info from decoded token
            $email = $decodedToken->email ?? null;
            $name = $decodedToken->name ?? 'Google User';

            if (!$email) {
                return response()->json(['message' => 'Invalid token: email is missing'], 400);
            }


            // Find or create the user in the database
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'username' => $name,
                    'email_verified_at' => now(),
                    'password' => bcrypt(Str::random(32)), // Stronger default password
                    'address' => '',
                    'avatar' => asset('assets/img/avatar/avatar-4.png'),
                    'is_active' => true,
                    'role' => 2,
                    'phone_number' => '',
                    'latitude' => null,
                    'longitude' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Generate JWT token for the user
            $token = auth()->login($user);

            return $this->createNewToken($token);

        } catch (\Exception $e) {
            // Log the error for better debugging
            return response()->json(['message' => 'Failed to validate token: ' . $e->getMessage()], 400);
        }
    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ]);
    }
}
