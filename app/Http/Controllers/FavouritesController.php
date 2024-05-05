<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favourites;
use Illuminate\Support\Facades\Validator;

class FavouritesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/favourites",
     *     summary="Get all favourites",
     *     tags={"Favourites"},
     *     security={{"bearerAuth":{}}},

     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Favourites")
     *         ),
     *     ),
     * )
     */
    public function index()
    {
        $favourites = Favourites::all();
        return response()->json($favourites);
    }

    /**
     * @OA\Post(
     *     path="/api/favourites",
     *     summary="Create a new favourite",
     *     security={{"bearerAuth":{}}},

     *     tags={"Favourites"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"generate_date", "value", "name", "category_id"},
     *             @OA\Property(property="generate_date", type="string", example="2024-05-05"),
     *             @OA\Property(property="value", type="string", example="Some value"),
     *             @OA\Property(property="name", type="string", example="Favourite Name"),
     *             @OA\Property(property="category_id", type="integer", example="1")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Favourite created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Favourites")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'generate_date' => 'required|date',
            'value' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $favourite = Favourites::create($validator->validated());
        return response()->json($favourite, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/favourites/{id}",
     *     summary="Get a specific favourite",
     *     security={{"bearerAuth":{}}},
     *     tags={"Favourites"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Favourite ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Favourites")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Favourite not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Favourite not found")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $favourite = Favourites::find($id);
        if (!$favourite) {
            return response()->json(['message' => 'Favourite not found'], 404);
        }
        return response()->json($favourite);
    }

    /**
     * @OA\Put(
     *     path="/api/favourites/{id}",
     *     summary="Update a favourite",
     *     security={{"bearerAuth":{}}},
     *     tags={"Favourites"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Favourite ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="generate_date", type="string", example="2024-05-06"),
     *             @OA\Property(property="value", type="string", example="Updated value"),
     *             @OA\Property(property="name", type="string", example="Updated Favourite Name"),
     *             @OA\Property(property="category_id", type="integer", example="2")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Favourite updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Favourites")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Favourite not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Favourite not found")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $favourite = Favourites::find($id);
        if (!$favourite) {
            return response()->json(['message' => 'Favourite not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'generate_date' => 'required|date',
            'value' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $favourite->update($validator->validated());
        return response()->json($favourite);
    }

    /**
     * @OA\Delete(
     *     path="/api/favourites/{id}",
     *     summary="Delete a favourite",
     *     tags={"Favourites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Favourite ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Favourite deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Favourite not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Favourite not found")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $favourite = Favourites::find($id);
        if (!$favourite) {
            return response()->json(['message' => 'Favourite not found'], 404);
        }

        $favourite->delete();
        return response()->json(null, 204);
    }
}
