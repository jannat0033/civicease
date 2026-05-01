<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportDeletionCleansImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_a_report_removes_its_uploaded_image(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put('reports/test-image.jpg', 'image-content');

        $report = Report::factory()->create([
            'image_path' => 'reports/test-image.jpg',
        ]);

        $report->delete();

        Storage::disk('public')->assertMissing('reports/test-image.jpg');
    }

    public function test_deleting_a_user_account_removes_report_images_before_account_deletion(): void
    {
        Storage::fake('public');

        $resident = User::factory()->create();
        Storage::disk('public')->put('reports/profile-delete.jpg', 'image-content');

        Report::factory()->for($resident)->create([
            'image_path' => 'reports/profile-delete.jpg',
        ]);

        $this->actingAs($resident)
            ->delete(route('profile.destroy'), [
                'password' => 'password',
            ])
            ->assertRedirect('/');

        Storage::disk('public')->assertMissing('reports/profile-delete.jpg');
        $this->assertDatabaseMissing('reports', [
            'image_path' => 'reports/profile-delete.jpg',
        ]);
    }
}
