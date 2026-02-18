<?php

namespace App\Http\Controllers;

use App\Models\Competency;
use App\Models\Core;
use App\Models\Position;
use App\Models\Technical;
use App\Models\Leadership;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    // Legend mapping
    // private $legend = [
    //     0 => 'Not Applicable',
    //     1 => 'Basic',
    //     2 => 'Intermediate',
    //     3 => 'Advanced',
    //     4 => 'Superior'
    // ];

    // public function index()
    // {
    //     $positions = Position::all();
    //     return response()->json($positions);
    // }

    // public function core()
    // {
    //     $core = Core::all();
    //     return response()->json($core);
    // }

    // public function technical()
    // {
    //     $Technical = Technical::all();
    //     return response()->json($Technical);
    // }

    // public function leadership()
    // {
    //     $leadership = Leadership::all();
    //     return response()->json($leadership);
    // }

    // // New method to get position with competencies
    // public function showWithCompetencies($id)
    // {
    //     try {
    //         // Get the position
    //         $position = Position::find($id);

    //         if (!$position) {
    //             return response()->json([
    //                 'error' => 'Position not found',
    //                 'message' => 'The requested position does not exist'
    //             ], 404);
    //         }

    //         // Get related competencies
    //         $core = Core::find($position->core_id);
    //         $technical = Technical::find($position->technical_id);
    //         $leadership = Leadership::find($position->leadership_id);

    //         // Format the response with legend
    //         $response = [
    //             'position' => $position->name,
    //             'sg' => $position->sg,
    //             'level' => $position->level,
    //             'core' => $this->formatCompetencies($core),
    //             'technical' => $this->formatCompetencies($technical),
    //             'leadership' => $this->formatCompetencies($leadership)
    //         ];

    //         return response()->json($response);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => 'Server Error',
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // // Helper function to format competencies with legend
    // private function formatCompetencies($competency)
    // {
    //     if (!$competency) return null;

    //     $formatted = [];
    //     $attributes = $competency->getAttributes();

    //     foreach ($attributes as $key => $value) {
    //         if ($key === 'id' || $key === 'created_at' || $key === 'updated_at') continue;

    //         $formatted[$key] = [
    //             'value' => $value,
    //             'legend' => $this->legend[$value] ?? 'Unknown'
    //         ];
    //     }

    //     return $formatted;
    // }
}
