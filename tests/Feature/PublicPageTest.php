<?php

namespace Tests\Feature;

use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_are_available(): void
    {
        foreach ([
            route('home'),
            route('about'),
            route('privacy'),
            route('accessibility'),
            route('community-map'),
            route('help'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_community_map_only_shows_resolved_reports_with_safe_public_fields(): void
    {
        Report::factory()->create([
            'title' => 'Resolved streetlight',
            'category' => 'Broken streetlight',
            'status' => Report::STATUS_RESOLVED,
            'description' => 'This internal description should not appear on the public map.',
            'postcode' => 'SW1A 1AA',
            'address' => 'Hidden address',
            'latitude' => 51.501364,
            'longitude' => -0.141890,
        ]);

        Report::factory()->create([
            'title' => 'Submitted pothole',
            'category' => 'Pothole',
            'status' => Report::STATUS_SUBMITTED,
            'description' => 'This should not be public.',
            'postcode' => 'M1 1AE',
            'address' => 'Private address',
        ]);

        $this->get(route('community-map', [
            'category' => 'Broken streetlight',
        ]))
            ->assertOk()
            ->assertSee('Resolved streetlight')
            ->assertSee('Broken streetlight')
            ->assertDontSee('Submitted pothole')
            ->assertDontSee('This internal description should not appear on the public map.')
            ->assertDontSee('SW1A 1AA')
            ->assertDontSee('Hidden address')
            ->assertDontSee('51.501364')
            ->assertDontSee('-0.14189');
    }
}
