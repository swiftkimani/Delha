<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chama;
use App\Models\Member;
use App\Models\Sheet;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a Chama (group)
        $chama = Chama::create([
            'name' => 'Delha Investment Group',
            'code' => 'DELHA2025',
            'description' => 'Main chama for 2025 contributions',
        ]);

        // 2. Create members and assign them to this chama
        $members = [
            ['member_id' => 'MEMBER-00001', 'name' => 'John Wanjohi'],
            ['member_id' => 'MEMBER-00002', 'name' => 'Jamleck Ngari'],
            ['member_id' => 'MEMBER-00003', 'name' => 'Stephen Irungu'],
            ['member_id' => 'MEMBER-00004', 'name' => 'John Njue'],
            ['member_id' => 'MEMBER-00005', 'name' => 'Amos Gakungu'],
            // Add as many as you want
        ];

        foreach ($members as $m) {
            Member::create(array_merge($m, ['chama_id' => $chama->id]));
        }

        // 3. Create one sample sheet (not excluded)
        Sheet::create([
            'chama_id' => $chama->id,
            'name' => 'Week 004 - 9/23/2025',
            'upload_date' => '2025-09-23',
            'is_excluded' => false,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Chama: ' . $chama->name);
        $this->command->info('Members: ' . Member::count());
        $this->command->info('Sheets: ' . Sheet::count());
    }
}