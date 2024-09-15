<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExampleController extends Controller
{
    
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
