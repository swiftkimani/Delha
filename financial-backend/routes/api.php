<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UploadController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\MemberController;

Route::middleware('auth:sanctum')->group(function () {
    // Member Management
    // Route::apiResource('members', MemberController::class);
    
    // Sheet Upload
    Route::post('/upload-sheet', [UploadController::class, 'uploadSheet']);
    
    // Reports
    Route::get('/reports/cumulative', [ReportController::class, 'cumulativeReport']);
    Route::get('/reports/member/{memberId}', [ReportController::class, 'memberReport']);
    Route::get('/reports/export', [ReportController::class, 'exportReport']);
    
    // Dashboard Stats
    Route::get('/dashboard/stats', function () {
        return response()->json([
            'total_members' => \App\Models\Member::count(),
            'total_sheets' => \App\Models\Sheet::where('is_excluded', false)->count(),
            'total_transactions' => \App\Models\Payment::count(),
            'total_amount' => \App\Models\Payment::sum(DB::raw('savings + project + welfare + fine + others')),
        ]);
    });
});