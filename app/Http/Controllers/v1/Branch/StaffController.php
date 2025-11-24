<?php

namespace App\Http\Controllers\v1\Branch;

use App\Models\Branch\Staff;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\Branch\Setup\Counter;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\StaffResource;
use Illuminate\Support\Facades\Cache;
use App\Services\ClearCacheService;

class StaffController extends Controller
{
    //Display a listing of the resource.
    public function index(Request $request)
    {

        $staffId = Auth::guard('branchstaffs')->user()->staff_id ?? '';
        $branchId = $request->header('X-Branch-ID');

        $cursor = $request->get('cursor', 'first_page');
        $cacheKey = "staff_list_$branchId _ {$cursor}";
        $staffData = Cache::tags(`staff_list_{$branchId}`)->flexible($cacheKey, [now()->addDay(2), null], function () use ($staffId) {
            return Staff::with([
                'status:status_id,status_name',
                'roles:id,name',
                'permissions:id,name',
            ])->where('staff_id', '!=', $staffId)->orderBy('last_name', 'asc')->cursorPaginate(30);
        });

        if ($staffData->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No staff records found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' =>  StaffResource::collection($staffData),
            'pagination' => [
                'nextCursor' => $staffData->nextCursor()?->encode(),
                'previousCursor' => $staffData->previousCursor()?->encode(),
            ]
        ], 200);
    }

    //Store a newly created resource in storage.
    public function store(Request $request)
    {
        $request->validate([
            'firstName' => ['required', 'string', 'regex:/^[A-Za-z\s\'-]+$/', 'min:2', 'max:50'],
            'middleName' => ['nullable', 'string', 'regex:/^[A-Za-z\s\'-]+$/', 'min:2', 'max:50'],
            'lastName' => ['required', 'string', 'regex:/^[A-Za-z\s\'-]+$/', 'min:2', 'max:50'],
            'emailAddress' => 'required|string|email|max:255|unique:staff,email_address',
            'phoneNumber' => ['required', 'string', 'unique:staff,phone_number', 'regex:/^\+?[1-9]\d{1,14}$/',],
            'roleId' => 'integer|exists:roles,id',
        ]);

        $staffId = Counter::generateCustomId('STAF');
        $staff = Staff::create([
            'staff_id' => $staffId,
            'first_name' => strtoupper($request->firstName),
            'middle_name' => strtoupper($request->middleName),
            'last_name' => strtoupper($request->lastName),
            'email_address' => strtolower($request->emailAddress),
            'phone_number' => $request->phoneNumber,
            'password' => $staffId,
            'created_by' => Auth::guard('branchstaffs')->user()->staff_id ?? Auth::guard('centralstaffs')->user()->staff_id,
        ]);

        $role = Role::findById($request->roleId, 'branchstaffs');
        $staff->assignRole($role);

        $branchId = $request->header('X-Branch-ID');
        ClearCacheService::clearListCache(`staff_list_$branchId`);

        return response()->json([
            'success' => true,
            'message' => 'Staff created successfully.',
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
