<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UploadController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\MemberController;

Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'version' => '1.0',
        'time' => now(),
    ]);
});

// For now, disable auth for testing
Route::prefix('members')->group(function () {
    Route::get('/', [MemberController::class, 'index']);
    Route::post('/', [MemberController::class, 'store']);
    Route::get('/{id}', [MemberController::class, 'show']);
    Route::put('/{id}', [MemberController::class, 'update']);
    Route::delete('/{id}', [MemberController::class, 'destroy']);
});

Route::post('/upload-sheet', [UploadController::class, 'uploadSheet']);
Route::get('/reports/cumulative', [ReportController::class, 'cumulativeReport']);
Route::get('/reports/member/{memberId}', [ReportController::class, 'memberReport']);
Route::get('/reports/export', [ReportController::class, 'exportReport']);
Route::get('/dashboard/stats', function () {
    return response()->json([
        'total_members' => \App\Models\Member::count(),
        'total_sheets' => \App\Models\Sheet::where('is_excluded', false)->count(),
        'total_transactions' => \App\Models\Payment::count(),
        'total_amount' => \App\Models\Payment::sum(\DB::raw('savings + project + welfare + fine + others')),
    ]);
});