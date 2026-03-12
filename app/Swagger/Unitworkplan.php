<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;


/**
 *
 * @OA\Tag(
 *     name="Unit Work Plan",
 *     description=""
 * )
 *
 * @OA\Post(
 *     path="/api/unit_work_plan/store",
 *     summary="Add Unit Work Plan for employees",
 *     tags={"Unit Work Plan"},
 *      security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"employees"},
 *             @OA\Property(
 *                 property="employees",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     required={"control_no","semester","year","office","performance_standards"},
 *                     @OA\Property(property="control_no", type="string", example="003041"),
 *                     @OA\Property(property="semester", type="string", example="July-December"),
 *                     @OA\Property(property="year", type="integer", example=2026),
 *                     @OA\Property(property="office", type="string", example="ICT Office"),
 *                     @OA\Property(
 *                         property="performance_standards",
 *                         type="array",
 *                         @OA\Items(
 *                             type="object",
 *                             required={"category","success_indicator","performance_indicator","ratings","config"},
 *                             @OA\Property(property="category", type="string", example="B. CORE FUNCTION"),
 *                             @OA\Property(property="mfo", type="string", example="ICT Network and Data Management"),
 *                             @OA\Property(property="output", type="string", example="System Development"),
 *                             @OA\Property(property="output_name", type="string", example="Information System"),
 *                             @OA\Property(
 *                                 property="performance_indicator",
 *                                 type="array",
 *                                 @OA\Items(
 *                                     type="object",
 *                                     required={"category","value"},
 *                                     @OA\Property(property="category", type="string", example="Production"),
 *                                     @OA\Property(property="value", type="string", example="supervised")
 *                                 )
 *                             ),
 *                             @OA\Property(property="success_indicator", type="string", example="2 related ICT network and data management output supervised without lapses , as per schedule"),
 *                             @OA\Property(property="required_output", type="string", example="Completed software system"),
 *                             @OA\Property(
 *                                 property="ratings",
 *                                 type="array",
 *                                 @OA\Items(
 *                                     type="object",
 *                                     @OA\Property(property="rating", type="integer", example=5),
 *                                     @OA\Property(property="quantity", type="string", example="3"),
 *                                     @OA\Property(property="effectiveness", type="string", example="All systems deployed without issues"),
 *                                     @OA\Property(property="timeliness", type="string", example="Within 6 months")
 *                                 )
 *                             ),
 *                             @OA\Property(
 *                                 property="config",
 *                                 type="object",
 *                                 required={"target_output","quantity_indicator","timeliness_indicator","timelinessType"},
 *                                 @OA\Property(property="target_output", type="string", example="3 systems"),
 *                                 @OA\Property(property="quantity_indicator", type="string", example="Number of systems developed"),
 *                                 @OA\Property(property="timeliness_indicator", type="string", example="Project completion time"),
 *                                 @OA\Property(
 *                                     property="timelinessType",
 *                                     type="object",
 *                                     required={"range","date","description"},
 *                                     @OA\Property(property="range", type="boolean", example=true),
 *                                     @OA\Property(property="date", type="boolean", example=false),
 *                                     @OA\Property(property="description", type="boolean", example=true)
 *                                 )
 *                             )
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *  @OA\Response(
 *         response=200,
 *         description="Unit Work Plan created successfully"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Unauthorized. Please login to access this resource."),
 *             @OA\Property(property="error", type="string", example="Unauthenticated")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to create Unit Work Plan"
 *     )
 * )
 */



    class Unitworkplan{}
