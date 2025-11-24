<?php

namespace App\Http\Controllers\v1\Central;

use Illuminate\Http\Request;
use App\Models\Central\Staff;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\StaffResource;
use App\Models\Central\Setup\Counter;
use Illuminate\Support\Facades\Cache;
use App\Services\ClearCacheService;

class StaffController extends Controller
{

    public function index(Request $request)
    {
        $staffId = Auth::guard('centralstaffs')->user()->staff_id ?? '';

        $cursor = $request->get('cursor', 'first_page');
        $cacheKey = "staff_list_{$cursor}";
        $staffData = Cache::tags('staff_list')->flexible($cacheKey, [now()->addDay(2), null], function () use ($staffId) {
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

    public function store(Request $request)
    {
        $request->validate([
            'firstName' => ['required', 'string', 'regex:/^[A-Za-z\s\'-]+$/', 'min:2', 'max:50'],
            'middleName' => ['nullable', 'string', 'regex:/^[A-Za-z\s\'-]+$/', 'min:2', 'max:50'],
            'lastName' => ['required', 'string', 'regex:/^[A-Za-z\s\'-]+$/', 'min:2', 'max:50'],
            'emailAddress' => 'required|string|email|max:255|unique:staff,email',
            'phoneNumber' => ['required', 'string', 'unique:staff,phone_number', 'regex:/^\+?[1-9]\d{1,14}$/',],
            'roleId' => 'integer|exists:roles,id',
        ]);

        $staffId = Counter::generateCustomId('STAF');
        $staff = Staff::create([
            'staff_id' => $staffId,
            'first_name' => strtoupper($request->firstName),
            'middle_name' => strtoupper($request->middleName),
            'last_name' => strtoupper($request->lastName),
            'email' => strtolower($request->emailAddress),
            'phone_number' => $request->phoneNumber,
            'password' => $staffId,
            'created_by' => Auth::guard('centralstaffs')->user()->staff_id ?? null,
        ]);

        $role = Role::findById($request->roleId, 'centralstaffs');
        $staff->assignRole($role);

        ClearCacheService::clearListCache('staff_list');

        return response()->json([
            'success' => true,
            'message' => 'Staff created successfully.',
        ], 201);
    }

    public function show(string $id)
    {
        $staffData = Cache::remember("staff_profile_{$id}", now()->addMonth(), function () use ($id) {
            return new StaffResource(Staff::with([
                'status:status_id,status_name',
                'roles:id,name',
                'permissions:id,name',
            ])->FindOrFail($id));
        });

        return response()->json([
            'success' => true,
            'data' => $staffData,
        ]);
    }

    public function update(Request $request, string $id)
    {
        //
    }
}
