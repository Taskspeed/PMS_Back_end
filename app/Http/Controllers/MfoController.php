<?php

namespace App\Http\Controllers;

use App\Models\mfo;
use Illuminate\Http\Request;

class MfoController extends Controller
{
    //

    // Handle MFO Creation
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'office_id' => 'required|exists:offices,id',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ]);

        // Create a new MFO
        mfo::create([
            'office_id' => $request->office_id,
            'name' => $request->name,
            'category' => $request->category,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'MFO added successfully!',
        ]);
    }
}
