<?php

namespace App\Http\Controllers\Planning;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Unit_work_plan;
use App\Models\Office;


class Planning_Unit_work_planController extends Controller
{
    public function office(Request $request)
    {
        $query = Office::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $office = $query->get();
        return response()->json($office);
    }

 
    public function employee()
    {
        $data = Employee::all();
        return response()->json($data);
    }

}
