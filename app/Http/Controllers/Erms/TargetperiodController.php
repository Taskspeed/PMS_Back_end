<?php

namespace App\Http\Controllers\Erms;

use App\Http\Controllers\Controller;
use App\Models\TargetPeriodLib;
use Illuminate\Http\Request;

class TargetperiodController extends Controller
{
       // get the lastest targetPeriod created by hr admin
        public function lastestTargetPeriods()
        {
            $targetPeriod = TargetPeriodLib::select('id', 'year', 'semester', 'created_at')
                ->latest()
                ->first();

            return response()->json($targetPeriod);
        }

}
