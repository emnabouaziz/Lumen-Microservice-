<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Posts",
 *     description="Operations related to posts"
 * )
 */
class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/posts",
     *     summary="Get a list of posts",
     *     tags={"Posts"},
     *     @OA\Response(
     *         response=200,
     *         description="List of posts retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Post")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $posts = Post::paginate(10);
        return response()->json($posts);
    }

    /**
     * @OA\Get(
     *     path="/posts/{id}",
     *     summary="Get a post by ID",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     )
     * )
     */
    public function show($id)
    {
        $post = Post::with('tags')->findOrFail($id);
        return response()->json($post);
    }

    /**
     * @OA\Post(
     *     path="/posts",
     *     summary="Create a new post",
     *     tags={"Posts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $post = Post::create($request->all());
        return response()->json($post, 201);
    }

    /**
     * @OA\Put(
     *     path="/posts/{id}",
     *     summary="Update an existing post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $post->update($request->all());
        return response()->json($post, 200);
    }

    /**
     * @OA\Delete(
     *     path="/posts/{id}",
     *     summary="Delete a post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete(); // Soft delete

        return response()->json(['message' => 'Post soft deleted successfully']);
    }

    /**
     * @OA\Post(
     *     path="/posts/{id}/restore",
     *     summary="Restore a soft deleted post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post to restore",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post restored successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found or not soft deleted"
     *     )
     * )
     */
    public function restore($id)
    {
        $post = Post::withTrashed()->findOrFail($id);

        if ($post->deleted_at !== null) {
            $post->restore(); // Restauration
            return response()->json(['message' => 'Post restored successfully']);
        }

        return response()->json(['error' => 'Post not found or not soft deleted'], 404);
    }

    /**
     * @OA\Delete(
     *     path="/posts/{id}/force",
     *     summary="Force delete a post",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post to force delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post force deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found or not soft deleted"
     *     )
     * )
     */
    public function forceDelete($id)
    {
        $post = Post::withTrashed()->findOrFail($id);

        if ($post->deleted_at !== null) {
            $post->forceDelete(); // Suppression physique
            return response()->json(['message' => 'Post hard deleted successfully']);
        }

        return response()->json(['error' => 'Post not found or not soft deleted'], 404);
    }

    /**
     * @OA\Get(
     *     path="/posts/soft-deleted",
     *     summary="Get a list of soft deleted posts",
     *     tags={"Posts"},
     *     @OA\Response(
     *         response=200,
     *         description="List of soft deleted posts retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Post")
     *         )
     *     )
     * )
     */
    public function getSoftDeleted()
    {
        $posts = Post::onlyTrashed()->paginate(10);
        return response()->json($posts);
    }

    /**
     * @OA\Get(
     *     path="/posts/search",
     *     summary="Search posts by title or content",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         required=true,
     *         description="Search query string",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Posts found matching the search query",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Post")
     *         )
     *     )
     * )
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $posts = Post::where('title', 'like', "%$query%")
                     ->orWhere('content', 'like', "%$query%")
                     ->paginate(10);
        return response()->json($posts);
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
