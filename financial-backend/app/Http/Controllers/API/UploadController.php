<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Sheet;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;

class UploadController extends Controller
{
    public function uploadSheet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv',
            'sheet_name' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Auto-exclude specific sheets
        $isExcluded = str_contains($request->sheet_name, 'Sep 9 - Sep 30 (2025)');
        
        $sheet = Sheet::create([
            'name' => $request->sheet_name,
            'upload_date' => now(),
            'is_excluded' => $isExcluded,
        ]);
        
        $file = $request->file('file');
        $data = Excel::toArray([], $file)[0];
        
        DB::beginTransaction();
        try {
            foreach ($data as $index => $row) {
                if ($index === 0) continue; // Skip header
                
                // Validate numeric fields
                if (!is_numeric($row[2] ?? 0)) continue;
                
                $member = Member::firstOrCreate(
                    ['member_id' => $row[0]],
                    ['name' => $row[1] ?? 'Unknown', 'status' => 'active']
                );
                
                Payment::create([
                    'member_id' => $row[0],
                    'sheet_id' => $sheet->id,
                    'date' => now(),
                    'savings' => $row[2] ?? 0,
                    'project' => $row[3] ?? 0,
                    'welfare' => $row[4] ?? 0,
                    'fine' => $row[5] ?? 0,
                    'others' => $row[6] ?? 0,
                ]);
            }
            
            DB::commit();
            return response()->json(['message' => 'Sheet uploaded successfully'], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Upload failed: ' . $e->getMessage()], 500);
        }
    }
}