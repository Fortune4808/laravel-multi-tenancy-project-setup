<?php

namespace App\Http\Controllers\v1\Central;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    //Display a listing of the resource.
    public function index()
    {
        $authGuard = Auth::getDefaultDriver();
        $permissions = Cache::flexible('permissions' . $authGuard, [3600, 600], function () {
            return Permission::select('id', 'name')->get();
        });
        return response()->json([
            'data' => $permissions,
        ], 200);
    }
}
