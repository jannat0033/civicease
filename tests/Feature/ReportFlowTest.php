<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ReportFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_report_create_page(): void
    {
        $this->get(route('reports.create'))->assertRedirect(route('login'));
    }

    public function test_resident_can_submit_report(): void
    {
        $user = User::factory()->create();
        Http::fake([
            'https://api.postcodes.io/postcodes/*' => Http::response([
                'status' => 200,
                'result' => [
                    'latitude' => 51.501364,
                    'longitude' => -0.141890,
                ],
            ], 200),
        ]);

        $response = $this->actingAs($user)->post(route('reports.store'), [
            'category' => 'Pothole',
            'title' => 'Large pothole on main road',
            'description' => 'There is a deep pothole causing traffic disruption and possible vehicle damage.',
            'postcode' => 'SW1A 1AA',
            'address' => 'Main Road junction',
            'latitude' => '51.5013640',
            'longitude' => '-0.1418900',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reports', [
            'title' => 'Large pothole on main road',
            'user_id' => $user->id,
        ]);
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_RESIDENT]);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_can_update_report_status(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $report = Report::factory()->create();

        $this->actingAs($admin)->patch(route('admin.reports.status.update', $report), [
            'status' => Report::STATUS_RESOLVED,
            'note' => 'Completed by maintenance team.',
        ])->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => Report::STATUS_RESOLVED,
        ]);
    }
}
