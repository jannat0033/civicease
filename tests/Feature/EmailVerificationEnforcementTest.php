<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationEnforcementTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_resident_is_redirected_away_from_verified_routes(): void
    {
        $resident = User::factory()->unverified()->create();

        $this->actingAs($resident)
            ->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));

        $this->actingAs($resident)
            ->get(route('reports.create'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verified_resident_can_access_verified_routes(): void
    {
        $resident = User::factory()->create();

        $this->actingAs($resident)
            ->get(route('dashboard'))
            ->assertOk();

        $this->actingAs($resident)
            ->get(route('reports.create'))
            ->assertOk();
    }
}
