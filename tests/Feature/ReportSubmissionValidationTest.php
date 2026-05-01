<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ReportSubmissionValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_postcode_is_rejected_server_side(): void
    {
        $resident = User::factory()->create();

        Http::fake([
            'https://api.postcodes.io/postcodes/*' => Http::response([
                'status' => 404,
                'error' => 'Invalid postcode',
            ], 404),
        ]);

        $this->actingAs($resident)
            ->from(route('reports.create'))
            ->post(route('reports.store'), $this->validPayload())
            ->assertRedirect(route('reports.create'))
            ->assertSessionHasErrors('postcode');
    }

    public function test_pin_too_far_from_postcode_is_rejected_server_side(): void
    {
        $resident = User::factory()->create();

        Http::fake([
            'https://api.postcodes.io/postcodes/*' => Http::response([
                'status' => 200,
                'result' => [
                    'latitude' => 51.501364,
                    'longitude' => -0.141890,
                ],
            ], 200),
        ]);

        $payload = $this->validPayload();
        $payload['latitude'] = '53.4807590';
        $payload['longitude'] = '-2.2426310';

        $this->actingAs($resident)
            ->from(route('reports.create'))
            ->post(route('reports.store'), $payload)
            ->assertRedirect(route('reports.create'))
            ->assertSessionHasErrors(['latitude', 'longitude']);
    }

    /**
     * @return array<string, string>
     */
    protected function validPayload(): array
    {
        return [
            'category' => 'Pothole',
            'title' => 'Large pothole on main road',
            'description' => 'There is a deep pothole causing traffic disruption and possible vehicle damage.',
            'postcode' => 'SW1A 1AA',
            'address' => 'Main Road junction',
            'latitude' => '51.5013640',
            'longitude' => '-0.1418900',
        ];
    }
}
