<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportStatusHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'CivicEase Admin',
            'email' => 'admin@example.com',
            'password' => 'Admin123!',
            'role' => User::ROLE_ADMIN,
            'postcode' => 'SW1A 1AA',
        ]);

        $resident = User::factory()->create([
            'name' => 'Sample Resident',
            'email' => 'resident@example.com',
            'password' => 'Password123!',
            'role' => User::ROLE_RESIDENT,
            'postcode' => 'M1 1AE',
        ]);

        $reports = Report::factory()->count(6)->for($resident)->create();

        foreach ($reports as $report) {
            ReportStatusHistory::create([
                'report_id' => $report->id,
                'status' => $report->status,
                'note' => 'Seeded status for demo purposes.',
                'updated_by' => $admin->id,
            ]);
        }
    }
}
