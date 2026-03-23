<?php

namespace App\Swagger\Planning\Dashboard;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/api/planning/dashboard/list-pending-opcr/{semester}/{year}",
 *     summary="List of OPCR Pending",
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
 *         description="List of pending OPCR",
 *         @OA\JsonContent(
 *             type="array",
 *             example={
 *                 {
 *                     "id": 1,
 *                     "office_name": "OFFICE OF THE CITY INFORMATION AND COMMUNICATIONS TECHNOLOGY MANAGEMENT OFFICER",
 *                     "semester": "July-December",
 *                     "year": 2026,
 *                     "date": "03-19-2026",
 *                     "status": "Pending",
 *                     "office_head_name": "JOSEPH NELSON N. BRIONES",
 *                     "control_no": "003041"
 *                 },
 *                 {
 *                     "id": 2,
 *                     "office_name": "OFFICE OF THE CITY HUMAN RESOURCE MANAGEMENT OFFICER",
 *                     "semester": "July-December",
 *                     "year": 2026,
 *                     "date": "03-19-2026",
 *                     "status": "Pending",
 *                     "office_head_name": "KRISTINE JOYCE C. ISRAEL",
 *                     "control_no": "018692"
 *                 }
 *             },
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="office_name", type="string"),
 *                 @OA\Property(property="semester", type="string"),
 *                 @OA\Property(property="year", type="integer"),
 *                 @OA\Property(property="date", type="string"),
 *                 @OA\Property(property="status", type="string"),
 *                 @OA\Property(property="office_head_name", type="string"),
 *                 @OA\Property(property="control_no", type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="No data available",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="There is no data available yet.")
 *         )
 *     )
 * )
 */
class PendingOpcr {}
