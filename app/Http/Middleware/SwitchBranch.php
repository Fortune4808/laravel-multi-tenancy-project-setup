<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Central\Branch;
use Illuminate\Support\Facades\Cache;
use App\Services\BranchConnectionService;
use Symfony\Component\HttpFoundation\Response;

class SwitchBranch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $branchId = $request->header('X-Branch-ID');
        if (!$branchId) {
            return response()->json([
                'success' => false,
                'message' => 'Branch ID header is missing.'
            ], 400);
        }
        $branchDb = Cache::flexible('branch_conn_' . $branchId, [now()->addMonth(), null], function () use ($branchId) {
            return Branch::where('branch_id', $branchId)->first();
        });
        
        if ($branchDb) {
            BranchConnectionService::connectToBranch($branchDb->database_name);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Branch ID.'
            ], 400);
        }
        return $next($request);
    }
}
