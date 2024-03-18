<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate user and generate JWT token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="password", type="string", example="password123")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Login successful"),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = JWTAuth::fromUser(Auth::user());
        Cookie::queue('jwt_token', $token, 180);

        return response()->json(['token' => $token]);
    }
    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Invalidate JWT token to logout user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="Logout successful"),
     *     @OA\Response(response="500", description="Failed to logout")
     * )
     *
     * Logout user by invalidating JWT token
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Attempt to invalidate the JWT token
        try {
            JWTAuth::parseToken()->invalidate();
            return response()->json(['message' => 'Logout successful']);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['error' => 'Token expired'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'Token invalid'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Failed to logout'], 500);
        }
    }

}
