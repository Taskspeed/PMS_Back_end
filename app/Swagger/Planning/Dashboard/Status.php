<?php

namespace App\Swagger\Planning\Dashboard;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/api/planning/dashboard/status/{semester}/{year}",
 *     summary="Total Opcr, Reviewed, Returned, Draft",
 *     tags={"Planning / Dashboard"},
 *     @OA\Parameter(
 *         name="semester",
 *         in="path",
 *         required=true,
 *         description="Semester (January-June, July-December)",
 *         @OA\Schema(type="string", example="January-June")
 *     ),
 *     @OA\Parameter(
 *         name="year",
 *         in="path",
 *         required=true,
 *         description="Year",
 *         @OA\Schema(type="integer", example=2026)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Total count of Status",
 *    @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="opcr_status",
 *                 type="object",
 *                 @OA\Property(property="Total", type="integer", example=10),
 *                 @OA\Property(property="Reviewed", type="integer", example=5),
 *                 @OA\Property(property="Pending", type="integer", example=3),
 *                 @OA\Property(property="Draft", type="integer", example=2)
 *             )
 *         )
 *     )
 * )
 */
 
class Status {}
