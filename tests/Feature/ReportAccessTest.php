<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_resident_cannot_view_another_users_report(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $report = Report::factory()->for($owner)->create();

        $this->actingAs($otherUser)
            ->get(route('reports.show', $report))
            ->assertForbidden();
    }

    public function test_admin_can_view_any_report(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $report = Report::factory()->for($owner)->create();

        $this->actingAs($admin)
            ->get(route('reports.show', $report))
            ->assertOk();
    }
}
