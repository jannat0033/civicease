<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_resident_can_submit_report_with_image_and_initial_status_history(): void
    {
        Storage::fake('public');
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

        $response = $this->actingAs($resident)->post(route('reports.store'), [
            'category' => 'Graffiti',
            'title' => 'Graffiti on the underpass wall',
            'description' => 'Large graffiti has appeared on the underpass wall and needs removal for visibility and safety.',
            'postcode' => 'SW1A 1AA',
            'address' => 'Underpass entrance',
            'latitude' => '51.5013640',
            'longitude' => '-0.1418900',
            'image' => UploadedFile::fake()->createWithContent(
                'graffiti.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a6xQAAAAASUVORK5CYII=')
            ),
        ]);

        $response->assertRedirect();

        $report = Report::query()->latest('id')->firstOrFail();

        $this->assertSame(Report::STATUS_SUBMITTED, $report->status);
        $this->assertNotNull($report->image_path);
        Storage::disk('public')->assertExists($report->image_path);
        $this->assertDatabaseHas('report_status_histories', [
            'report_id' => $report->id,
            'status' => Report::STATUS_SUBMITTED,
            'note' => 'Report submitted by resident.',
            'updated_by' => $resident->id,
        ]);
    }
}
