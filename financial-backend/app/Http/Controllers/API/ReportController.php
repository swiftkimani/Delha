<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function cumulativeReport()
    {
        $results = DB::table('members as m')
            ->select([
                'm.member_id',
                'm.name',
                DB::raw('COALESCE(SUM(p.savings), 0) as savings_total'),
                DB::raw('COALESCE(SUM(p.project), 0) as project_total'),
                DB::raw('COALESCE(SUM(p.welfare), 0) as welfare_total'),
                DB::raw('COALESCE(SUM(p.fine), 0) as fine_total'),
                DB::raw('COALESCE(SUM(p.others), 0) as others_total'),
                DB::raw('COALESCE(SUM(p.savings + p.project + p.welfare + p.fine + p.others), 0) as grand_total'),
            ])
            ->leftJoin('payments as p', 'm.member_id', '=', 'p.member_id')
            ->leftJoin('sheets as s', 'p.sheet_id', '=', 's.id')
            ->where('s.is_excluded', false)
            ->orWhereNull('s.id')
            ->groupBy('m.member_id', 'm.name')
            ->get();
        
        return response()->json($results);
    }
    
    public function memberReport($memberId)
    {
        $member = Member::with(['payments.sheet' => function ($query) {
            $query->where('is_excluded', false);
        }])->findOrFail($memberId);
        
        $totals = DB::table('payments as p')
            ->select([
                DB::raw('COALESCE(SUM(p.savings), 0) as savings_total'),
                DB::raw('COALESCE(SUM(p.project), 0) as project_total'),
                DB::raw('COALESCE(SUM(p.welfare), 0) as welfare_total'),
                DB::raw('COALESCE(SUM(p.fine), 0) as fine_total'),
                DB::raw('COALESCE(SUM(p.others), 0) as others_total'),
                DB::raw('COALESCE(SUM(p.savings + p.project + p.welfare + p.fine + p.others), 0) as grand_total'),
            ])
            ->join('sheets as s', 'p.sheet_id', '=', 's.id')
            ->where('p.member_id', $memberId)
            ->where('s.is_excluded', false)
            ->first();
        
        return response()->json([
            'member' => $member,
            'totals' => $totals,
            'payments' => $member->payments
        ]);
    }
    
    public function exportReport(Request $request)
    {
        $report = $this->cumulativeReport();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="cumulative_report_' . date('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($report) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Member ID', 'Name', 'Savings Total', 'Project Total',
                'Welfare Total', 'Fine Total', 'Others Total', 'Grand Total'
            ]);
            
            // Add data
            foreach ($report->getData() as $row) {
                fputcsv($file, [
                    $row->member_id,
                    $row->name,
                    $row->savings_total,
                    $row->project_total,
                    $row->welfare_total,
                    $row->fine_total,
                    $row->others_total,
                    $row->grand_total,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}