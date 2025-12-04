<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\Sheet;
use App\Models\Payment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create members
        $members = [
            ['member_id' => 'MEM001', 'name' => 'John Doe', 'status' => 'active'],
            ['member_id' => 'MEM002', 'name' => 'Jane Smith', 'status' => 'active'],
            ['member_id' => 'MEM003', 'name' => 'Robert Johnson', 'status' => 'active'],
            ['member_id' => 'MEM004', 'name' => 'Emily Davis', 'status' => 'inactive'],
            ['member_id' => 'MEM005', 'name' => 'Michael Brown', 'status' => 'active'],
        ];

        foreach ($members as $member) {
            Member::create($member);
        }

        // Create sheets (excluding Sep sheet)
        $sheets = [
            ['name' => 'January 2024 Contributions', 'upload_date' => '2024-01-31', 'is_excluded' => false],
            ['name' => 'February 2024 Contributions', 'upload_date' => '2024-02-29', 'is_excluded' => false],
            ['name' => 'March 2024 Contributions', 'upload_date' => '2024-03-31', 'is_excluded' => false],
            ['name' => 'Sep 9 - Sep 30 (2025)', 'upload_date' => '2025-09-30', 'is_excluded' => true],
        ];

        foreach ($sheets as $sheet) {
            Sheet::create($sheet);
        }

        // Create sample payments
        $sheetIds = Sheet::where('is_excluded', false)->pluck('id');
        $memberIds = Member::pluck('member_id');

        foreach ($sheetIds as $sheetId) {
            foreach ($memberIds as $memberId) {
                Payment::create([
                    'member_id' => $memberId,
                    'sheet_id' => $sheetId,
                    'date' => now()->subMonths(rand(1, 3)),
                    'savings' => rand(1000, 5000),
                    'project' => rand(500, 3000),
                    'welfare' => rand(200, 1500),
                    'fine' => rand(0, 1000),
                    'others' => rand(0, 2000),
                ]);
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Total Members: ' . Member::count());
        $this->command->info('Total Sheets: ' . Sheet::count());
        $this->command->info('Total Payments: ' . Payment::count());
    }
}