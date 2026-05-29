<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\vwActive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class vwActiveController extends Controller
{



    const ALLOWED_ROLES = [1, 2, 3, 4, 5, 6];

    public function getOfficeEmployee(Request $request)
    {
        $office_name = $request->query('office_name');

        if (!$office_name) {
            return response()->json([
                'message' => 'office_name is required'
            ], 422);
        }

        $totalRoles = count(self::ALLOWED_ROLES); // 6

        // Get control_no of employees who already have ALL 6 roles
        $fullyRegisteredControlNos = User::whereNotNull('control_no')
            ->whereIn('role_id', self::ALLOWED_ROLES)
            ->groupBy('control_no')
            ->havingRaw('COUNT(DISTINCT role_id) >= ?', [$totalRoles])
            ->pluck('control_no')
            ->toArray();

        $data = vwActive::select(
            'ControlNo',
            'BirthDate',
            'Office',
            'name4',
            'Designation',
            'status'
        )
            ->where('Office', $office_name)
            // ✅ Only exclude employees who have ALL roles already
            ->whereNotIn('ControlNo', $fullyRegisteredControlNos)
            ->get();

        return response()->json($data);
    }
}
