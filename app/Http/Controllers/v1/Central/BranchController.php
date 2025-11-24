<?php

namespace App\Http\Controllers\v1\Central;

use Illuminate\Support\Str;
use App\Jobs\SetupNewBranch;
use Illuminate\Http\Request;
use App\Models\Central\Branch;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BranchResource;

class BranchController extends Controller
{
    //Display a listing of the resource.
    public function index()
    {
        $branches = Branch::cursorPaginate(30);
        if ($branches->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No branches found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => BranchResource::collection($branches),
            'pagination' => [
                'per_page' => $branches->perPage(),
                'next_page_url' => $branches->nextPageUrl(),
            ]
        ], 200);
    }

    //Store a newly created resource in storage.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branchName' => ['required', 'string', 'min:2', 'max:50', 'unique:branches,branch_name'],
        ]);

        
        $dbName = Str::slug($validated['branchName'], '_') . '_db';
        $branchName = strtoupper($validated['branchName']);
        $createdBy = Auth::guard('centralstaffs')->user()->staff_id;

        SetupNewBranch::dispatch($branchName, $dbName, $createdBy);
        
        return response()->json([
            'success' => true,
            'message' => 'Branch creation job queued. Database setup in progress.',
        ], 202);
    }

    //Display the specified resource.
    public function show(string $id)
    {
        //
    }

    //Update the specified resource in storage.
    public function update(Request $request, string $id)
    {
        //
    }
}
