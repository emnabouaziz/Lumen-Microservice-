<?php

namespace App\Http\Controllers\API\V1;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\ApiHelpers;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Services\Pagination;
use App\Services\PaginationGenerator;
use Exception;
use App\Services\ElasticsearchService;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
class PostController extends BaseController
{ 
    protected $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
        
    }

     /**
     * Initialize Elasticsearch index and index existing posts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function initializeIndex()
    {
        // Initialize the index
        $this->elasticsearchService->initializeIndex('posts');

        // Fetch all existing posts from the database
        $posts = Post::all();

        // Index each post
        foreach ($posts as $post) {
            $this->elasticsearchService->indexDocument('posts', $post->id, $post->toArray());
        }

        return response()->json(['message' => 'Index initialized and data indexed']);
    }

    /**
 * @OA\Get(
 *      path="/posts",
 *      tags={"Post"},
 *      summary="Get Posts",
 *      description="Get Post",
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
 *          @OA\JsonContent(
 *              type="object",
 *             
 *          )
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
    $msg   = ApiHelpers::API_RESP_MSG_GET_SUCCESS;
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
            $msg  = ApiHelpers::API_RESP_MSG_BAD_REQUEST;
            $data = $validator->errors()->messages();
        } else {
            // Charger les relations 'tags' avec chaque 'Post'
          /*  $posts = Post::with('tags')->orderBy('id', 'DESC')->paginate($_GET['per_page']);
            $data['posts'] = $posts;*/
            $cacheKey = 'posts_' . $params['per_page'];
            $cachedPosts = Redis::get($cacheKey);
            if (!$cachedPosts) {
                $posts = Post::with('tags')->orderBy('id', 'DESC')->paginate($_GET['per_page']);
                Redis::set($cacheKey, serialize($posts));Log::info("from db");
            } else {
                $posts = unserialize($cachedPosts);
                Log::info("from cash");
            }
            
            $data['posts'] = $posts;
        }
    } catch (Exception $e) {
        $code  = Response::HTTP_INTERNAL_SERVER_ERROR;
        $msg   = ApiHelpers::API_RESP_MSG_INTERNAL_SERVER_ERROR;
    } finally {
        return ApiHelpers::createApiResponse($code, $msg, $data);
    }
}

    /**
     * @OA\Get(
     *     path="/posts/{id}",
     *     summary="Get a post by ID",
     *     tags={"Post"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the post to retrieve",
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
        try {
            $cacheKey = 'post_' . $id;
            $cachedPost = Redis::get($cacheKey);

            if (!$cachedPost) {
                $post = Post::with('tags')->findOrFail($id);
                Redis::set($cacheKey, serialize($post));
            } else {
                $post = unserialize($cachedPost);
            }
            //$post = Post::with('tags')->findOrFail($id);
            $code = Response::HTTP_OK;
            $msg = ApiHelpers::API_RESP_MSG_GET_SUCCESS;
            $data = $post;
        } catch (Exception $e) {
            $code = Response::HTTP_NOT_FOUND;
            $msg = ApiHelpers::API_RESP_MSG_NOT_FOUND;
            $data = null;
        }

        return ApiHelpers::createApiResponse($code, $msg, $data);
    }
/**
 * @OA\Post(
 *      path="/posts",
 *      operationId="StorePost",
 *      tags={"Post"},
 *      summary="Create a New Post",
 *      description="Create a new Post",
 *      @OA\RequestBody(
 *          required=true,
 *          description="Post data",
 *          @OA\JsonContent(
 *              required={"title", "content", "is_default", "can_delete"},
 *              @OA\Property(property="title", type="string"),
 *              @OA\Property(property="content", type="string"),
 *              @OA\Property(property="is_default", type="boolean"),
 *              @OA\Property(property="can_delete", type="boolean"),
 *          ),
 *      ),
 *      @OA\Response(
 *          response=201,
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


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_default' => 'required|boolean',
            'can_delete' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return ApiHelpers::createApiResponse(
                Response::HTTP_BAD_REQUEST,
                ApiHelpers::API_RESP_MSG_BAD_REQUEST,
                $validator->errors()
            );
        }

        try {
            $post = Post::create($request->all());
            $code = Response::HTTP_CREATED;
            $msg = ApiHelpers::API_RESP_MSG_POST_SUCCESS;
            $data = $post;
        } catch (Exception $e) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $msg = ApiHelpers::API_RESP_MSG_INTERNAL_SERVER_ERROR;
            $data = null;
        }

        return ApiHelpers::createApiResponse($code, $msg, $data);
    }

/**
 * @OA\Put(
 *      path="/posts/{id}",
 *      operationId="UpdatePost",
 *      tags={"Post"},
 *      summary="Update an Existing Post",
 *      description="Update an existing Post",
 *      @OA\Parameter(
 *          name="id",
 *          description="ID of the post to update",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\RequestBody(
 *          required=true,
 *          description="Updated post data",
 *          @OA\JsonContent(
 *              @OA\Property(property="title", type="string", example="Updated Title"),
 *              @OA\Property(property="content", type="string", example="Updated Content"),
 *              @OA\Property(property="is_default", type="boolean", example=true),
 *              @OA\Property(property="can_delete", type="boolean", example=true),
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *        
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad Request",
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthenticated",
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Forbidden",
 *      )
 * )
 */
public function update(Request $request, $id)
{
    // Valider les paramètres de la requête
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'is_default' => 'required|boolean',
        'can_delete' => 'required|boolean',
    ]);

    // Vérifier s'il y a des erreurs de validation
    if ($validator->fails()) {
        return ApiHelpers::createApiResponse(
            Response::HTTP_BAD_REQUEST,
            ApiHelpers::API_RESP_MSG_BAD_REQUEST,
            $validator->errors()
        );
    }

    try {
        $post = Post::findOrFail($id);
        $post->update($request->all());
        $code = Response::HTTP_OK;
        $msg = ApiHelpers::API_RESP_MSG_UPDATE_SUCCESS;
        $data = $post;
    } catch (\Exception $e) {
        $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        $msg = ApiHelpers::API_RESP_MSG_INTERNAL_SERVER_ERROR;
        $data = null;
    }

    // Retourner la réponse au format JSON
    return ApiHelpers::createApiResponse($code, $msg, $data);
}
/**
 * @OA\Delete(
 *      path="/posts/{id}",
 *      operationId="DeletePost",
 *      tags={"Post"},
 *      summary="Soft Delete a Post",
 *      description="Soft delete a post",
 *
 *      @OA\Parameter(
 *          name="id",
 *          description="ID of the post to delete",
 *          required=true,
 *          in="path",
 *         
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad Request"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthenticated"
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Forbidden"
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Post not found or not soft deleted"
 *      )
 * )
 */
public function destroy($id)
{
    try {
        $post = Post::findOrFail($id);

        if ($post->deleted_at !== null) {
            $code = Response::HTTP_BAD_REQUEST;
            $msg = 'Post already soft deleted';
            $data = ['error' => $msg];
        } else {
            $post->delete(); // Soft deletion
            $code = Response::HTTP_OK;
            $msg = 'Item deleted.';
            $data = $post; // Return the deleted post data with modified deleted_at
        }

        return ApiHelpers::createApiResponse($code, $msg, $data);
    } catch (\Exception $e) {
        $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        $msg = 'Internal Server Error';
        $data = ['error' => $msg];

        return ApiHelpers::createApiResponse($code, $msg, $data);
    }
}

/**
 * @OA\Put(
 *      path="/posts/{id}/restore",
 *      operationId="RestorePost",
 *      tags={"Post"},
 *      summary="Restore a Soft Deleted Post",
 *      description="Restore a soft deleted post by setting deleted_at to null",
 *
 *      @OA\Parameter(
 *          name="id",
 *          description="ID of the post to restore",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *         
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Post not found or not soft deleted",
 *         
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Internal Server Error",
 *         
 *      )
 * )
 */
public function restore($id)
{
    try {
        $post = Post::withTrashed()->findOrFail($id);

        if ($post->deleted_at !== null) {
            $post->deleted_at = null;
            $post->save();

            $code = Response::HTTP_OK;
            $msg = 'Post restored successfully';
            $data = $post;
        } else {
            $code = Response::HTTP_NOT_FOUND;
            $msg = 'Post not found or not soft deleted';
            $data = null;
        }

        return ApiHelpers::createApiResponse($code, $msg, $data);
    } catch (\Exception $e) {
        $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        $msg = 'Internal Server Error';
        $data = null;

        return ApiHelpers::createApiResponse($code, $msg, $data);
    }
}


   /**
 * @OA\Delete(
 *      path="/posts/{id}/force",
 *      operationId="ForceDeletePost",
 *      tags={"Post"},
 *      summary="Force Delete a Post",
 *      description="Force delete a Post permanently",
 *
 * @OA\Parameter(
 *          name="id",
 *          description="ID of the post to force delete",
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
    try {
        $post = Post::withTrashed()->findOrFail($id);

        if ($post->deleted_at !== null) {
            $post->forceDelete(); // Suppression physique
            $code = Response::HTTP_OK;
            $msg = 'Post hard deleted successfully';
            $data =  null;
        } else {
            $code = Response::HTTP_NOT_FOUND;
            $msg = 'Post not found or not soft deleted';
            $data = ['error' => $msg];
        }

        return ApiHelpers::createApiResponse($code, $msg, $data);
    } catch (\Exception $e) {
        $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        $msg = 'Internal Server Error';
        $data = ['error' => $msg];

        return ApiHelpers::createApiResponse($code, $msg, $data);
    }
}

  
/**
 * @OA\Get(
 *      path="/posts/soft-deleted",
 *      operationId="GetAllSoftDeletedPosts",
 *      tags={"Post"},
 *      summary="Get All Soft Deleted Posts",
 *      description="Retrieve all soft deleted posts",
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
public function getSoftDeleted()
{
    $code = Response::HTTP_OK;
    $msg = ApiHelpers::API_RESP_MSG_GET_SUCCESS;
    $data = [];

    try {
        $perPage = request()->input('per_page', 10); // Default per page

        $validator = Validator::make(
            ['per_page' => $perPage],
            ['per_page' => 'required|integer']
        );

        if ($validator->fails()) {
            $code = Response::HTTP_UNPROCESSABLE_ENTITY;
            $msg = ApiHelpers::API_RESP_MSG_BAD_REQUEST;
            $data = $validator->errors()->messages();
        } else {
            // Get soft deleted posts with pagination
            $data = Post::onlyTrashed()->orderBy('id', 'DESC')->with('tags')->paginate($perPage);
        }
    } catch (\Exception $e) {
        $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        $msg = ApiHelpers::API_RESP_MSG_INTERNAL_SERVER_ERROR;
    } finally {
        return ApiHelpers::createApiResponse($code, $msg, $data);
    }
}
   /**
 * @OA\Get(
 *     path="/posts/search",
 *     summary="Search posts by title",
 *     tags={"Post"},
 *     @OA\Parameter(
 *         name="query",
 *         in="query",
 *         required=true,
 *         description="Search query string",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *          
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
public function search(Request $request)
{
    // Récupérer le paramètre de requête 'query'
    $text = $request->input('query');

    // Vérifier si le champ de recherche 'query' est vide
    if (empty($text)) {
        return response()->json(['error' => 'Le paramètre "query" est requis pour la recherche.'], 400);
    }

    // Appeler la méthode search du service Elasticsearch
    $results = $this->elasticsearchService->search('posts', 'title', $text);

    // Vérifier si des résultats ont été trouvés
    if (isset($results['hits']['hits']) && count($results['hits']['hits']) > 0) {
        // Extraire les données des résultats
        $formattedResults = [];
        foreach ($results['hits']['hits'] as $hit) {
            $formattedResults[] = $hit['_source'];
        }

        // Retourner les résultats formatés en JSON
        return response()->json(['data' => $formattedResults]);
    } else {
        // Retourner une réponse vide si aucun résultat n'est trouvé
        return response()->json(['message' => 'Aucun résultat trouvé pour la recherche spécifiée.']);
    }}
}

