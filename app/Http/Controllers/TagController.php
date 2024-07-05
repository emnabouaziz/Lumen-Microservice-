<?php
namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Post;
use Illuminate\Http\Request;

class TagController extends Controller
{   /**
    * @OA\Get(
    *     path="/tags",
    *     summary="Get a list of tags",
    *     @OA\Response(
    *         response=200,
    *         description="List of tags retrieved successfully",
    *         @OA\JsonContent(
    *             type="array",
    *             @OA\Items(ref="#/components/schemas/Tag")
    *         )
    *     )
    * )
    */
    public function index()
    {
        $tags = Tag::all();
        return response()->json($tags);
    }
  /**
 * @OA\Post(
 *     path="/posts/{postId}/tags/{tagId}",
 *     summary="Attach a tag to an existing post",
 *     @OA\Parameter(
 *         name="postId",
 *         in="path",
 *         required=true,
 *         description="ID of the post to attach the tag to",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="tagId",
 *         in="path",
 *         required=true,
 *         description="ID of the tag to attach to the post",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Tag attached to post successfully"
 *     )
 * )
 */
    public function store(Request $request, $postId)
    {
        // Vérifiez si le post existe
        $post = Post::findOrFail($postId);

        // Créez un nouveau tag
        $tag = new Tag();
        $tag->name = $request->input('name');
        $tag->save();

        // Attachez automatiquement le tag au post
        $post->tags()->attach($tag->id);

        return response()->json(['message' => 'Tag created and attached to post successfully', 'tag' => $tag]);
    }

    public function show($id)
    {
        $tag = Tag::findOrFail($id);
        return response()->json($tag);
    }
/**
 * @OA\Put(
 *     path="/tags/{id}",
 *     summary="Update an existing tag",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the tag to update",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/Tag")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Tag updated successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Tag")
 *     )
 * )
 */
    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $tag->name = $request->input('name');
        $tag->save();

        return response()->json(['message' => 'Tag updated successfully', 'tag' => $tag]);
    }
/**
 * @OA\Delete(
 *     path="/tags/{id}",
 *     summary="Delete a tag",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the tag to delete",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Tag deleted successfully"
 *     )
 * )
 */
    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete(); // Soft delete

        return response()->json(['message' => 'Tag soft deleted successfully']);
    }
/**
 * @OA\Post(
 *     path="/tags/{id}/restore",
 *     summary="Restore a soft deleted tag",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the tag to restore",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Tag restored successfully",
 *         @OA\JsonContent(ref="#/components/schemas/Tag")
 *     )
 * )
 */
    public function restore($id)
    {
        $tag = Tag::onlyTrashed()->findOrFail($id);
        $tag->restore();

        return response()->json(['message' => 'Tag restored successfully']);
    }

    public function forceDelete($id)
    {
        $tag = Tag::onlyTrashed()->findOrFail($id);
        $tag->forceDelete(); // Hard delete

        return response()->json(['message' => 'Tag hard deleted successfully']);
    }
    public function search(Request $request)
    {
        $name = $request->input('name');

        // Utilisez Eloquent pour rechercher les tags par le nom
        $tags = Tag::where('name', 'like', "%$name%")->get();

        return response()->json($tags);
    }
}
