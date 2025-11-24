<?php

namespace App\Http\Controllers\v1\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    //Display a listing of the resource.
    public function index()
    {
        //
    }

    //Store a newly created resource in storage.
    public function store(Request $request)
    {
        $incomingField = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        Role::create([
            'name' => $incomingField['name'],
            'guard_name' => 'centralstaffs',
        ])->syncPermissions($incomingField['permissions']);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully.'
        ], 201);
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

    //Remove the specified resource from storage.
    public function destroy(string $id)
    {
        //
    }
}
