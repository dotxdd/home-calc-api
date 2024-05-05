<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Favourites;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class IdeaController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/generate-text",
     *     summary="Generate text using GPT-3.5",
     *     security={{"bearerAuth":{}}},
     *     tags={"GPT"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"text", "category_id"},
     *             @OA\Property(property="text", type="string", example="Your text here"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Generated text successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="generated_text", type="string", example="Generated text here"),
     *             ),
     *             @OA\Property(property="status_page", type="integer", example=200),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="Category ID is missing"),
     *             ),
     *             @OA\Property(property="status_page", type="integer", example=400),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="API key is missing"),
     *             ),
     *             @OA\Property(property="status_page", type="integer", example=401),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="Category not found"),
     *             ),
     *             @OA\Property(property="status_page", type="integer", example=404),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="message", type="string", example="Failed to generate text"),
     *                 @OA\Property(property="errors", type="string", example="Error message here"),
     *             ),
     *             @OA\Property(property="status_page", type="integer", example=500),
     *         ),
     *     ),
     * )
     */

    public function generateText(Request $request)
    {
        try {
            // Get the authenticated user
            $user = $request->user();
            // Check if the user has an API key
            if (!$user->api_key) {
                return response()->json(['data' => ['message' => 'API key is missing'], 'status_page' => 401]);
            }

            // Get text and category ID from request
            $text = $request->input('text');
            $categoryId = $request->input('category_id');

            // Check if category ID is provided
            if (!$categoryId) {
                return response()->json(['data' => ['message' => 'Category ID is missing'], 'status_page' => 400]);
            }

            // Find category by ID
            $category = Category::find($categoryId);

            // Check if category exists
            if (!$category) {
                return response()->json(['data' => ['message' => 'Category not found'], 'status_page' => 404]);
            }

            // Construct prompt message including category name
            $prompt = "Generate text for category: {$category->name}. {$text}";

            // Make a request to OpenAI API
            $client = new Client();
            $response = $client->post('https://api.openai.com/v1/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $user->api_key,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'prompt' => $prompt,
                    'max_tokens' => 100,
                ],
            ]);

            // Extract generated text from response
            $generatedText = json_decode($response->getBody()->getContents())->choices[0]->text;

            // Return the generated text
            return response()->json(['data' => ['generated_text' => $generatedText], 'status_page' => 200]);
        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json(['data' => ['message' => 'Failed to generate text', 'errors' => $e->getMessage()], 'status_page' => 500]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/favourites/idea",
     *     summary="Create a new favourite",
     *     description="Insert a new favourite into the database",
     *     operationId="addFav",
     *     tags={"Favourites"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Favourite data",
     *         @OA\JsonContent(
     *             required={"value", "name", "category_id"},
     *             @OA\Property(property="value", type="string", example="Some value"),
     *             @OA\Property(property="name", type="string", example="Favourite Name"),
     *             @OA\Property(property="category_id", type="integer", format="int64", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Favourite created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Favourites")
     *     )
     * )
     *
     */
    public function addFav(Request $request)
    {
        $request->validate([
            'value' => 'required|string',
            'name' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        // Create a new Favourites instance
        $favourite = new Favourites();
        $favourite->generate_date = now(); // Set generate_date as the current date
        $favourite->value = $request->input('value');
        $favourite->name = $request->input('name');
        $favourite->user_id = auth()->id(); // Get the authenticated user's ID
        $favourite->category_id = $request->input('category_id');
        $favourite->save();

        return response()->json($favourite, 200);
    }

}
