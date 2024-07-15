<?php
namespace App\Http\Controllers\API\V1;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\ApiHelpers;
use App\Models\Tag;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\Pagination;
use App\Services\PaginationGenerator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TagController extends BaseController
{    /**
    * @OA\Get(
    *      path="/tags",
    *      tags={"Tag"},
    *      summary="Get Tags",
    *      description="Get Tags",
    *
    *      @OA\Parameter(
    *         name="per_page",
    *         description="Per Page",
    *         in="query",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Successful operation",
    *       ),
    *      @OA\Response(
    *          response=400,
    *          description="Bad Request"
    *      ),
    *      @OA\Response(
    *          response=401,
    *          description="Unauthenticated",
    *      ),
    *      @OA\Response(
    *          response=403,
    *          description="Forbidden"
    *      )
    * )
    */
   public function index()
   {
       $code  = Response::HTTP_OK;
       $msg  = 'Tags retrieved successfully';
       $data  = [];
       try {
           $params = [
               'per_page'  => $_GET['per_page'] ?? null,
           ];
           $validator = Validator::make(
               $params,
               [
                   'per_page'  => 'required|integer',
               ]
           );
           if ($validator->fails()) {
               $code = Response::HTTP_UNPROCESSABLE_ENTITY;
               $msg  = 'Validator error';
               $data = $validator->errors()->messages();
           } else {
               $data = Tag::orderBy('id', 'DESC')->paginate($_GET['per_page']);
           }
       } catch (Exception $e) {
           $code  = Response::HTTP_INTERNAL_SERVER_ERROR;
           $msg   = $e->getMessage();
       } finally {
           return response()->json(['code' => $code, 'message' => $msg, 'data' => $data], $code);
       }
   }

   /**
     * @OA\Post(
     *      path="/tags",
     *      operationId="StoreTag",
     *      tags={"Tag"},
     *      summary="Create a New Tag",
     *      description="Create a new Tag",
     *
     * @OA\Parameter(
     *          name="name",
     *          description="Name of the tag",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     * @OA\Parameter(
     *          name="is_default",
     *          description="Is default tag",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="boolean"
     *          )
     *      ),
     * @OA\Parameter(
     *          name="can_delete",
     *          description="Can delete tag",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="boolean"
     *          )
     *      ),
     * @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *       ),
     * @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     * @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
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
 /**
     * @OA\Get(
     *     path="/tags/{id}",
     *     summary="Get a tag by ID",
     *     tags={"Tag"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the tag to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function show($id)
    {
        $tag = Tag::findOrFail($id);
        return response()->json($tag);
    }

    /**
     * @OA\Put(
     *      path="/tags/update/{id}",
     *      operationId="UpdateTag",
     *      tags={"Tag"},
     *      summary="Update an Existing Tag",
     *      description="Update an existing Tag",
     *
     * @OA\Parameter(
     *          name="id",
     *          description="ID of the tag to update",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Parameter(
     *          name="name",
     *          description="Name of the tag",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     * @OA\Parameter(
     *          name="is_default",
     *          description="Is default tag",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="boolean"
     *          )
     *      ),
     * @OA\Parameter(
     *          name="can_delete",
     *          description="Can delete tag",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="boolean"
     *          )
     *      ),
     * @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     * @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     * @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
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
     *      path="/tags/{id}",
     *      operationId="SoftDeleteTag",
     *      tags={"Tag"},
     *      summary="Soft Delete a Tag",
     *      description="Soft delete a Tag",
     *
     * @OA\Parameter(
     *          name="id",
     *          description="ID of the tag to soft delete",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     * @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     * @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
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
     *      path="/restore/tags/{id}",
     *      operationId="RestoreTag",
     *      tags={"Tag"},
     *      summary="Restore a Soft Deleted Tag",
     *      description="Restore a soft deleted tag",
     *
     * @OA\Parameter(
     *          name="id",
     *          description="ID of the tag to restore",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     * @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     * @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function restore($id)
    {
        $tag = Tag::onlyTrashed()->findOrFail($id);
        $tag->restore();

        return response()->json(['message' => 'Tag restored successfully']);
    }

    /**
     * @OA\Delete(
     *      path="/tags/force-delete/{id}",
     *      operationId="ForceDeleteTag",
     *      tags={"Tag"},
     *      summary="Force Delete a Tag",
     *      description="Force delete a tag permanently",
     *
     * @OA\Parameter(
     *          name="id",
     *          description="ID of the tag to force delete",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     * @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     * @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     * @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     * @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
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
