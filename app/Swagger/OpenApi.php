<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="MAIFP API Docs",
 *         description="Swagger documentation for MAIFP project"
 *     ),
 *     @OA\Server(
 *         url="http://192.168.8.182:8000",
 *         description="Local development server"
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class OpenApi {}
