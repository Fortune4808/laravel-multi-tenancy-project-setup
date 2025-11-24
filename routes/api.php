<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\Central\RoleController;
use App\Http\Controllers\v1\Branch\RoleController as BranchRoleController;
use App\Http\Controllers\v1\Central\StaffController;
use App\Http\Controllers\v1\Central\BranchController;
use App\Http\Controllers\v1\Central\Auth\AuthenticationController;
use App\Http\Controllers\v1\Branch\StaffController as BranchStaffController;
use App\Http\Controllers\v1\Branch\Auth\AuthenticationController as BranchAuthenticationController;
use App\Http\Controllers\v1\Central\PermissionController;

Route::prefix('v1')->group(function () {
    Route::prefix('central')->group(function () {
        Route::post('auth/login', [AuthenticationController::class, 'login']);
        Route::post('auth/password/reset', [AuthenticationController::class, 'sendPasswordResetLink']);
        Route::post('auth/password/finishresetpassword', [AuthenticationController::class, 'finishResetPassword']);

        Route::middleware('auth:centralstaffs')->group(function () {
            Route::get('auth/profile', [AuthenticationController::class, 'profile']);
            Route::post('auth/logout', [AuthenticationController::class, 'logout']);

            Route::apiResource('staff', StaffController::class)->middleware('permission:manage staff');
            Route::apiResource('branch', BranchController::class)->middleware('permission:manage branches');
            Route::apiResource('role', RoleController::class)->middleware('permission:manage roles');
            Route::apiResource('permission', PermissionController::class)->only('index')->middleware('permission:manage roles');

            Route::prefix('branches')->middleware(['switch.branch'])->group(function () {
                Route::apiResource('branchstaffs', BranchStaffController::class)->middleware('permission:manage branch staff');
                Route::apiResource('role', BranchRoleController::class)->middleware('permission:manage branch roles');
            });
        });
    });

    Route::prefix('branch')->middleware(['identify.branch'])->group(function () {
        Route::post('auth/login', [BranchAuthenticationController::class, 'login']);

        Route::middleware('auth:branchstaffs')->group(function () {
            Route::apiResource('branchstaff', BranchStaffController::class)->middleware('permission:manage staff');
            Route::apiResource('role', BranchRoleController::class)->middleware('permission:manage roles');
            Route::apiResource('permission', PermissionController::class)->only('index')->middleware('permission:manage roles');
        });
    });
});
