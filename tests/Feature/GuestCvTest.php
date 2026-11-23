<?php

namespace Tests\Feature;

use App\Models\Cv;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestCvTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cv_saves_the_selected_template(): void
    {
        $response = $this->post('/create-cv', [
            'title' => 'My CV',
            'template' => 'executive',
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+2348000000000',
        ]);

        $cv = Cv::query()->sole();

        $response->assertRedirect(route('guest-cvs.preview', [
            'token' => $cv->guest_token,
        ]));
        $this->assertSame('executive', $cv->template);
        $this->assertSame('pending', $cv->payment_status);
        $this->assertFalse($cv->is_paid);
    }

    public function test_simple_guest_cv_is_free(): void
    {
        $this->post('/create-cv', [
            'title' => 'My CV',
            'template' => 'simple',
            'full_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+2348000000000',
        ]);

        $cv = Cv::query()->sole();

        $this->assertSame('free', $cv->payment_status);
        $this->assertFalse($cv->is_paid);
    }
}
