<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_status_update_creates_audit_history_entry(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $report = Report::factory()->create([
            'status' => Report::STATUS_SUBMITTED,
        ]);

        $this->actingAs($admin)->patch(route('admin.reports.status.update', $report), [
            'status' => Report::STATUS_IN_REVIEW,
            'note' => 'Assigned to the highways review queue.',
        ])->assertRedirect(route('admin.reports.show', $report));

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => Report::STATUS_IN_REVIEW,
        ]);
        $this->assertDatabaseHas('report_status_histories', [
            'report_id' => $report->id,
            'status' => Report::STATUS_IN_REVIEW,
            'note' => 'Assigned to the highways review queue.',
            'updated_by' => $admin->id,
        ]);
    }

    public function test_resident_cannot_open_admin_report_management_pages(): void
    {
        $resident = User::factory()->create(['role' => User::ROLE_RESIDENT]);
        $report = Report::factory()->create();

        $this->actingAs($resident)->get(route('admin.reports.index'))->assertForbidden();
        $this->actingAs($resident)->get(route('admin.reports.show', $report))->assertForbidden();
    }

    public function test_resident_cannot_update_report_status_through_admin_route(): void
    {
        $resident = User::factory()->create(['role' => User::ROLE_RESIDENT]);
        $report = Report::factory()->create([
            'status' => Report::STATUS_SUBMITTED,
        ]);

        $this->actingAs($resident)->patch(route('admin.reports.status.update', $report), [
            'status' => Report::STATUS_RESOLVED,
            'note' => 'Attempted unauthorized update.',
        ])->assertForbidden();

        $this->assertDatabaseMissing('report_status_histories', [
            'report_id' => $report->id,
            'status' => Report::STATUS_RESOLVED,
            'note' => 'Attempted unauthorized update.',
        ]);
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => Report::STATUS_SUBMITTED,
        ]);
    }
}
