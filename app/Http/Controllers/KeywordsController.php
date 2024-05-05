<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Keywords;
use Illuminate\Support\Facades\Validator;

class KeywordsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/keywords",
     *     summary="Get all keywords",
     *     security={{"bearerAuth":{}}},

     *     tags={"Keywords"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Keyword")
     *         ),
     *     ),
     * )
     */
    public function index()
    {
        $keywords = Keywords::all();
        return response()->json($keywords);
    }

    /**
     * @OA\Post(
     *     path="/api/keywords",
     *     summary="Create a new keyword",
     *     security={{"bearerAuth":{}}},

     *     tags={"Keywords"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"value"},
     *             @OA\Property(property="value", type="string", example="Keyword Value")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Keyword created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Keyword")
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
            'value' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $keyword = Keywords::create($validator->validated());
        return response()->json($keyword, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/keywords/{id}",
     *     summary="Get a specific keyword",
     *     security={{"bearerAuth":{}}},

     *     tags={"Keywords"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Keyword ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Keyword")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Keyword not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Keyword not found")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $keyword = Keywords::find($id);
        if (!$keyword) {
            return response()->json(['message' => 'Keyword not found'], 404);
        }
        return response()->json($keyword);
    }

    /**
     * @OA\Put(
     *     path="/api/keywords/{id}",
     *     summary="Update a keyword",
     *     tags={"Keywords"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Keyword ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="value", type="string", example="Updated Keyword Value")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Keyword updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Keyword")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Keyword not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Keyword not found")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $keyword = Keywords::find($id);
        if (!$keyword) {
            return response()->json(['message' => 'Keyword not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'value' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation error', 'errors' => $validator->errors()], 422);
        }

        $keyword->update($validator->validated());
        return response()->json($keyword);
    }

    /**
     * @OA\Delete(
     *     path="/api/keywords/{id}",
     *     summary="Delete a keyword",
     *     tags={"Keywords"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Keyword ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Keyword deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Keyword not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Keyword not found")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $keyword = Keywords::find($id);
        if (!$keyword) {
            return response()->json(['message' => 'Keyword not found'], 404);
        }

        $keyword->delete();
        return response()->json(null, 204);
    }
}
