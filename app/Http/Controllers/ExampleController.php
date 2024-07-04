<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExampleController extends Controller
{
    /**
    * @OA\Get(
    *     path="/sample/{category}/things",
    *     operationId="getSampleThings",
    *     tags={"sample"},
    *     summary="Get sample things by category",
    *     description="Returns a list of sample things",
    *     @OA\Parameter(
    *         name="category",
    *         in="path",
    *         description="The category parameter in path",
    *         required=true,
    *         @OA\Schema(type="string")
    *     ),
    *     @OA\Parameter(
    *         name="criteria",
    *         in="query",
    *         description="Some optional other parameter",
    *         required=false,
    *         @OA\Schema(type="string")
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful response",
    *         @OA\JsonContent(
    *             type="array",
    *             @OA\Items(type="string")
    *         )
    *     ),
    *     @OA\Response(
    *         response=400,
    *         description="Bad request"
    *     )
    * )
    */
    public function getThings(Request $request, $category)
    {
        $criteria = $request->input("criteria");
        if (!isset($category)) {
            return response()->json(null, Response::HTTP_BAD_REQUEST);
        }

        // Return sample data
        return response()->json(["thing1", "thing2"], Response::HTTP_OK);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
}
