<?php
namespace App\Http\Controllers;
use Laravel\Lumen\Routing\Controller as BaseController;
use Darkaonline\L5Swagger\Annotations as OA;
/**
 * @OA\Info(
 *      version="3.0.0",
 *      title="Microservice Test Lumen Project API Documentation",
 *      description="Enjoy with our documentation of Microservice Lumen Test API.",
 *      @OA\Contact(
 *          email="admin@admin.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url=SWAGGER_LUME_CONST_HOST,
 *      description="Demo API Server"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearer",
 *      type="http",
 *      scheme="Bearer",
 *      bearerFormat="JWT",
 * )
 */
class Controller extends BaseController
{
}