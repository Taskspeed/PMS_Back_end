<?php

namespace App\Swagger\Erms;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/api/erms/employee/target-periods/details/{targetPeriodId}/{month}/{year}/{week}",
 *     summary="Get Target Period with Performance Standards and Ratings",
 *     description="Retrieves target period details including performance standards, standard outcomes, performance ratings filtered by month, year, and week based on calendar week, and configurations.",
 *     tags={"ERMS / Target Period"},
 *     security={{"sanctum": {}}},
 *
 *     @OA\Parameter(
 *         name="targetPeriodId",
 *         in="path",
 *         required=true,
 *         description="Target Period ID",
 *         @OA\Schema(type="integer", example=71)
 *     ),
 *     @OA\Parameter(
 *         name="month",
 *         in="path",
 *         required=false,
 *         description="Full month name (e.g. January, February, December)",
 *         @OA\Schema(type="string", example="December")
 *     ),
 *     @OA\Parameter(
 *         name="year",
 *         in="path",
 *         required=false,
 *         description="4-digit year",
 *         @OA\Schema(type="integer", example=2026)
 *     ),
 *     @OA\Parameter(
 *         name="week",
 *         in="path",
 *         required=false,
 *         description="Calendar week of the month (week1, week2, week3, week4, week5)",
 *         @OA\Schema(
 *             type="string",
 *             enum={"week1", "week2", "week3", "week4", "week5"},
 *             example="week3"
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Target period details with filtered performance ratings
 *                      format date mm//dd//yy",
 *
 *         @OA\JsonContent(
 *             example={
 *                 "id": 71,
 *                 "performance_standards": {
 *                     {
 *                         "id": 235,
 *                         "target_period_id": "71",
 *                         "category": "B. CORE FUNCTION",
 *                         "mfo": "Design, Development and Deployment of Information Systems",
 *                         "output": "Information Systems Design and Development",
 *                         "output_name": "Computer Programmer in Information System Design and Development",
 *                         "performance_indicator": {
 *                             {
 *                                 "id": 6,
 *                                 "name": "supervised",
 *                                 "category_id": "3",
 *                                 "category": "Decision-Making",
 *                                 "value": "supervised"
 *                             }
 *                         },
 *                         "success_indicator": "3 Computer Programmer in Information System Design and Development supervised with minimal of less than 4 errors, in 6 months",
 *                         "required_output": "Number of Hired Computer Programmers",
 *                         "standard_outcomes": {
 *                             {
 *                                 "id": 1147,
 *                                 "performance_standard_id": "235",
 *                                 "rating": "5",
 *                                 "quantity": "3",
 *                                 "effectiveness": "with minimal of less than 4 errors",
 *                                 "timeliness": "in 4 months"
 *                             },
 *                             {
 *                                 "id": 1148,
 *                                 "performance_standard_id": "235",
 *                                 "rating": "4",
 *                                 "quantity": "2",
 *                                 "effectiveness": "with minimal of less than 6 errors",
 *                                 "timeliness": "in 5 months"
 *                             },
 *                             {
 *                                 "id": 1149,
 *                                 "performance_standard_id": "235",
 *                                 "rating": "3",
 *                                 "quantity": "1",
 *                                 "effectiveness": "with minimal of less than 8 errors",
 *                                 "timeliness": "in 6 months"
 *                             }
 *                         },
 *                         "performance_rating": {
 *                             {
 *                                 "id": 5264,
 *                                 "performance_standard_id": "235",
 *                                 "control_no": "011790",
 *                                 "date": "12/14/2026",
 *                                 "quantity_actual": 3,
 *                                 "effectiveness_actual": 15,
 *                                 "timeliness_actual": 15
 *                             },
 *                             {
 *                                 "id": 5265,
 *                                 "performance_standard_id": "235",
 *                                 "control_no": "011790",
 *                                 "date": "12/15/2026",
 *                                 "quantity_actual": 3,
 *                                 "effectiveness_actual": 15,
 *                                 "timeliness_actual": 15
 *                             },
 *                             {
 *                                 "id": 5266,
 *                                 "performance_standard_id": "235",
 *                                 "control_no": "011790",
 *                                 "date": "12/16/2026",
 *                                 "quantity_actual": 5,
 *                                 "effectiveness_actual": 25,
 *                                 "timeliness_actual": 25
 *                             }
 *                         },
 *                         "configurations": {
 *                             {
 *                                 "id": 233,
 *                                 "performance_standard_id": "235",
 *                                 "targetOutput": "3",
 *                                 "quantityIndicator": "numeric",
 *                                 "timelinessIndicator": "beforeDeadline",
 *                                 "range": "0",
 *                                 "date": "0",
 *                                 "description": "1"
 *                             }
 *                         }
 *                     }
 *                 }
 *             }
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Target period not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Target period not found.")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Invalid week provided",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Invalid week.")
 *         )
 *     )
 * )
 */
class TargetPeriodSummaryRating {}
