<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="failed logging in")
     *         )
     *     )
     * )
     */
    public function __invoke(Request $request){
        try {
            if (!auth()->attempt(['email' => $request->email, 'password' => $request->password])) {

                return response()->json(['data' => ['message' => 'Failed to login user','error' => 'failed logging in'],'status_page' => 401]);
            }

            $token = auth()->user()->createToken('personal_token')->plainTextToken;

            $cookie = cookie('auth_token', $token, 60 * 24 * 7);

            return response()->json(['data' =>['message' => 'Logged in user','token' => $token],'status_page' => 200], 200)->withCookie($cookie);
        }
        catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json(['data' => ['message' => 'Failed to login user', 'error' => $e->getMessage()], 'status_page' => 500]);
        }

    }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Get authenticated user",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated"),
     *         ),
     *     ),
     * )
     */
    public function getUser(Request $request)
    {
        try {
            // Get the authenticated user
            $user = $request->user();

            // Return a success response with the user data wrapped inside the "data" field
            return response()->json(['data' => ['status' => 'success', 'user' => $user]]);
        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json(['data' => ['status' => 'error', 'message' => 'Failed to retrieve user', 'error' => $e->getMessage()]], 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",

     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            // Create a new user
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // Return a success response with the user data wrapped inside the "data" field
            return response()->json(['data' => ['message' => 'User registered successfully', 'user' => $user], 'status_page' => 201], 201);
        } catch (ValidationException $e) {
            // Return a JSON response with validation errors
            return response()->json(['data' => ['message' => 'User registration failed', 'errors' => $e->validator->errors()->messages()], 'status_page' => 422]);
        } catch (\Exception $e) {
            // Return an error response if something else goes wrong
            return response()->json(['data' => ['message' => 'User registration failed', 'error' => $e->getMessage()], 'status_page' => 500], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response="200",
     *         description="User logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User logged out successfully")
     *         ),
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout failed"),
     *             @OA\Property(property="error", type="string", example="Internal Server Error")
     *         )
     *     ),
     * )
     */
    public function logout(Request $request)
    {
        try {
            // Revoke the user's token
            $request->user()->tokens()->delete();

            // Remove the token cookie
            $cookie = cookie()->forget('auth_token');

            // Return a success response
            return response()->json(['data' => ['message' => 'User logged out successfully'], 'status_page' => 200])->withCookie($cookie);
        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json(['data' =>['message' => 'Logout failed', 'error' => $e->getMessage()], 'status_page' => 500]);
        }
    }

}
