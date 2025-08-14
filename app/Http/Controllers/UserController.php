<?php

namespace App\Http\Controllers;

use App\Models\mfo;
use App\Models\Unit_work_plan;
use App\Models\vwActive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //

    public function get_user_data()
    {
        $user = Auth::user();
        $office = $user->office;

        // Get all categories having either MFOs or outputs for this office
        $categoryIds = $office->mfos()->pluck('f_category_id')
            ->merge($office->f_outpots()->pluck('f_category_id'))
            ->unique()
            ->toArray();

        $categories = \App\Models\F_category::whereIn('id', $categoryIds)->get();

        $result = [
            'user' => $user,
            'office' => $office,
            'categories' => []
        ];

        foreach ($categories as $category) {
            // MFOs for this office and category
            $mfos = $office->mfos()->where('f_category_id', $category->id)->get();
            $mfosArr = [];
            foreach ($mfos as $mfo) {
                $mfosArr[] = [
                    'id' => $mfo->id,
                    'name' => $mfo->name,
                    'outpots' => $mfo->outpots()->get()->toArray(),
                ];
            }

            // Outputs directly under category+office (not attached to any MFO)
            $outputs = $office->f_outpots()
                ->where('f_category_id', $category->id)
                ->whereNull('mfo_id')
                ->get();

            $result['categories'][] = [
                'id' => $category->id,
                'name' => $category->name,
                'mfos' => $mfosArr,
                'category_outputs' => $outputs,
            ];
        }

        return response()->json($result);
    }


    public function getUserUnitWorkPlans()
    {
        $user = Auth::user();
        $officeId = $user->office_id;

        // Fetch UnitWorkPlans for the authenticated user and their office
        $unitWorkPlans = Unit_work_plan::with(['office', 'employee'])
            ->where('employee_id', $user->id)
            ->where('office_id', $officeId)
            ->get();

        return response()->json([
            'unit_work_plans' => $unitWorkPlans
        ]);
    }


    public function get_user_info()
    {
        $users = vwActive::where('Surname', 'LIKE', '%mahusay%')->get();
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }
}
