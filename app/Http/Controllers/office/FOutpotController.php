<?php

namespace App\Http\Controllers\office;

use App\Http\Controllers\Controller;
use App\Http\Requests\Library\OutputRequest;

use App\Http\Requests\Library\OutputUpdateRequest;
use App\Models\F_outpot;
use App\Services\OutputService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Output\Output;

class FOutpotController extends Controller
{

    // storing output
    public function addOutput(OutputRequest $request,OutputService $outputService)
    {

        $output = $outputService->store($request->validated());

        return response()->json([
            'message' => 'Output created successfully',
            'output' => $output
        ]);
    }

    // update output
    public function updateOutput(OutputUpdateRequest $request, $id, OutputService $outputService)
    {

        $output = $outputService->update($request, $id);

        return response()->json([
            'message' => 'Output created successfully',
            'output' => $output
        ]);
    }


    // Delete for outputs
    public function deleteOutput($id)
    {
        $output = F_outpot::findOrFail($id); // Ensure the model use
        $output->delete();

        activity()
            ->performedOn($output)
            ->causedBy(Auth::user())
            ->withProperties(['name' =>  $output->name])
            ->log('Output deleted');

        return response()->json(['message' => 'Output deleted successfully']);
    }



}
