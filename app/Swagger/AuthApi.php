<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Authentication endpoints"
 * )
 *
 * @OA\Post(
 *     path="/api/login",
 *     summary="User login",
 *     tags={"Auth"},
 *     description="Authenticate a user and return access token",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","password"},
 *             @OA\Property(property="name", type="string", example="james"),
 *             @OA\Property(property="password", type="string", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful"
 *     )
 * )
 *
 */
class AuthApi {}
